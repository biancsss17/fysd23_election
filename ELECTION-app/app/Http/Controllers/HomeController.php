<?php

namespace App\Http\Controllers;

use App\Models\CandidateSubmission;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use App\Models\RegisteredVoter;
use App\Models\VoterPositionBallot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private const BALLOT_WINDOW_SECONDS = 60;

    /**
     * Display the application homepage.
     */
    public function index(): View
    {
        $position = ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->first()
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->first();
        $allPositionsComplete = ElectionPosition::query()->exists()
            && ! ElectionPosition::query()->where('is_completed', false)->exists();
        $submissions = CandidateSubmission::query()
            ->where('status', '!=', 'Rejected')
            ->where('position_id', $position?->id)
            ->get(['status']);

        return view('home', [
            'position' => $position,
            'timelineStep' => $allPositionsComplete ? 5 : $this->timelineStep($position, $submissions),
            'allPositionsComplete' => $allPositionsComplete,
        ]);
    }

    public function finalDocument(): View
    {
        $positions = ElectionPosition::query()
            ->orderBy('sort_order')->orderBy('id')
            ->get()
            ->map(function (ElectionPosition $position): array {
                $candidates = CandidateSubmission::query()
                    ->where('status', '!=', 'Rejected')
                    ->where('position_id', $position->id)
                    ->withCount('votes')
                    ->orderByDesc('votes_count')
                    ->orderBy('candidate_name')
                    ->get();

                return [
                    'position' => $position,
                    'winners' => $this->selectWinners($candidates, $position->seats),
                ];
            });

        return view('final-document', compact('positions'));
    }

    public function voterAccess(): View
    {
        $position = ElectionPosition::query()
            ->where('is_completed', false)
            ->where('is_closed', false)
            ->orderBy('sort_order')->orderBy('id')
            ->first()
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->first();

        if ($position?->is_unlocked && $position->unlocked_at
            && $position->unlocked_at->diffInSeconds(now()) >= self::BALLOT_WINDOW_SECONDS) {
            $this->recordAutomaticAbstentions($position);
            $position->update(['is_unlocked' => false, 'is_closed' => true, 'unlocked_at' => null]);
            $position->refresh();
        }

        $remainingSeconds = $position?->is_unlocked && $position->unlocked_at
            ? min(self::BALLOT_WINDOW_SECONDS, max(0, (int) floor(self::BALLOT_WINDOW_SECONDS - $position->unlocked_at->diffInSeconds(now()))))
            : 0;

        return response()
            ->view('voter-access', compact('position', 'remainingSeconds'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function verifyVoterAccess(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));

        $isRegistered = RegisteredVoter::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->exists();

        if (! $isRegistered) {
            return back()
                ->withInput()
                ->with('access_error', 'This email is not registered for the District 23 FYS election.');
        }

        $position = ElectionPosition::query()
            ->where('is_completed', false)
            ->where('is_closed', false)
            ->orderBy('sort_order')->orderBy('id')
            ->first();
        if (! $position || ! $position->is_unlocked) {
            return back()->withInput()->with('access_error', 'Voting has not started yet. Please wait for the administrator to unlock the ballot.');
        }

        if (ElectionVote::query()
            ->where('position_id', $position->id)
            ->where('voter_email', $email)
            ->exists()) {
            return back()
                ->withInput()
                ->with('access_error', 'This email has already submitted a vote for this position.');
        }

        session([
            'voter_access_email' => $email,
            'verified_email' => $email,
        ]);

        return redirect()->route('ballot');
    }

    public function ballot(?int $positionId = null): View|RedirectResponse
    {
        $position = $positionId
            ? ElectionPosition::query()->find($positionId)
            : ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->first();
        $position ??= ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->first()
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->first()
            ?? ElectionPosition::query()->orderBy('sort_order')->orderBy('id')->first();
        if ($position) {
            session(['current_position_id' => $position->id]);
            if ($position->is_unlocked && $position->unlocked_at
                && $position->unlocked_at->diffInSeconds(now()) >= self::BALLOT_WINDOW_SECONDS) {
                $this->recordAutomaticAbstentions($position);
                $position->update(['is_unlocked' => false, 'is_closed' => true, 'unlocked_at' => null]);
                $position->refresh();
            }
        }

        $remainingSeconds = $position?->is_unlocked && $position->unlocked_at
            ? min(self::BALLOT_WINDOW_SECONDS, max(0, (int) floor(self::BALLOT_WINDOW_SECONDS - $position->unlocked_at->diffInSeconds(now()))))
            : 0;
        return view('ballot', [
            'position' => $position,
            'candidateSubmissions' => CandidateSubmission::query()->where('status', '!=', 'Rejected')->where('position_id', $position?->id)->orderBy('id')->get(),
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function publicElectionData(): JsonResponse
    {
        $activePosition = ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->first()
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->first();
        if ($activePosition?->is_unlocked && $activePosition->unlocked_at
            && $activePosition->unlocked_at->diffInSeconds(now()) >= self::BALLOT_WINDOW_SECONDS) {
            $this->recordAutomaticAbstentions($activePosition);
            $activePosition->update(['is_unlocked' => false, 'is_closed' => true, 'unlocked_at' => null]);
        }
        $positions = ElectionPosition::query()->orderBy('sort_order')->orderBy('id')->get(['id', 'name', 'sort_order', 'seats', 'rule', 'allow_abstain', 'max_selections', 'is_completed', 'is_unlocked', 'is_closed', 'candidacy_open', 'nomination_open', 'unlocked_at', 'updated_at']);
        $remainingSeconds = $activePosition?->is_unlocked && $activePosition->unlocked_at
            ? min(self::BALLOT_WINDOW_SECONDS, max(0, (int) floor(self::BALLOT_WINDOW_SECONDS - $activePosition->unlocked_at->diffInSeconds(now()))))
            : 0;
        $positions->each(function (ElectionPosition $position) use ($activePosition, $remainingSeconds): void {
            $position->setAttribute('remaining_seconds', $activePosition?->id === $position->id ? $remainingSeconds : 0);
        });
        $activePosition?->setAttribute('remaining_seconds', $remainingSeconds);
        $submissions = CandidateSubmission::query()->where('status', '!=', 'Rejected')->where('position_id', $activePosition?->id)->orderBy('id')->get(['id', 'candidate_name', 'submitted_by_email', 'submission_type', 'status', 'position_id', 'updated_at']);

        return response()->json([
            'positions' => $positions,
            'active_position' => $activePosition,
            'latest_updated_at' => $positions->max('updated_at'),
            'submissions' => $submissions,
            'latest_submission_updated_at' => $submissions->max('updated_at'),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Display the final vote review screen.
     */
    public function reviewVote(): View
    {
        $choices = collect(session('pending_vote_choices', []));
        $verifiedEmail = session('verified_email');
        $position = ElectionPosition::query()->find(session('current_position_id'));
        $remainingSeconds = $position?->is_unlocked && $position->unlocked_at
            ? min(self::BALLOT_WINDOW_SECONDS, max(0, (int) floor(self::BALLOT_WINDOW_SECONDS - $position->unlocked_at->diffInSeconds(now()))))
            : 0;
        $votingOpen = (bool) ($position?->is_unlocked && ! $position->is_closed);
        $isVerified = is_string($verifiedEmail) && RegisteredVoter::query()
            ->where('email', $verifiedEmail)
            ->where('is_active', true)
            ->exists();
        $candidateIds = $choices
            ->filter(fn (string $choice): bool => str_starts_with($choice, 'candidate-'))
            ->map(fn (string $choice): int => (int) str_replace('candidate-', '', $choice));
        $candidateNames = CandidateSubmission::query()->whereIn('id', $candidateIds)->pluck('candidate_name')->values();

        return view('review-vote', [
            'position' => $position,
            'pendingChoices' => $choices->values(),
            'reviewChoices' => $candidateNames->isNotEmpty() ? $candidateNames : $choices->filter(fn (string $choice): bool => $choice !== 'abstain')->values(),
            'isVerified' => $isVerified,
            'votingOpen' => $votingOpen,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    /**
     * Display the completed ballot receipt.
     */
    public function voteReceipt(): View
    {
        return view('vote-receipt');
    }

    /**
     * Confirm that the submitted email belongs to an active registered voter.
     */
    public function verifyVoterEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'choices' => ['sometimes', 'array', 'min:1', 'max:10'],
            'choices.*' => ['required', 'string'],
        ]);

        if (! empty($validated['choices'])) {
            session(['pending_vote_choices' => $validated['choices']]);
        }

        $email = strtolower(trim($validated['email']));
        $isRegistered = RegisteredVoter::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->exists();

        if (! $isRegistered) {
            session()->forget('verified_email');

            return back()
                ->withInput()
                ->with('email_error', 'This email is not registered for the District 23 FYS election.');
        }

        $position = ElectionPosition::query()->find(session('current_position_id'))
            ?? ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->first()
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->first();
        if ($position?->is_unlocked && $position->unlocked_at
            && $position->unlocked_at->diffInSeconds(now()) >= self::BALLOT_WINDOW_SECONDS) {
            $this->recordAutomaticAbstentions($position);
            $position->update(['is_unlocked' => false, 'is_closed' => true, 'unlocked_at' => null]);

            return back()->with('email_error', 'The one-minute voting window has closed.');
        }
        if ($position && ElectionVote::query()
            ->where('position_id', $position->id)
            ->where('voter_email', $email)
            ->exists()) {
            session()->forget('verified_email');

            return back()->with('email_error', 'You have already voted for this position.');
        }

        session(['verified_email' => $email]);
        session()->keep('pending_vote_choices');

        return back()->with('email_success', 'Email verified. You may now submit your final vote.');
    }

    public function submitCandidacy(Request $request): RedirectResponse
    {
        $positionId = $this->activePositionId();
        $position = $positionId ? ElectionPosition::query()->find($positionId) : null;
        if (! $position || ! $position->candidacy_open) {
            return redirect()->route('home')->with('submission_error', 'Candidacy is currently closed by the administrator.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));
        $isRegistered = RegisteredVoter::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->exists();

        if (! $isRegistered) {
            return back()
                ->withInput()
                ->with('candidacy_error', 'You are not eligible to vote. Please contact an election administrator.');
        }

        if (CandidateSubmission::query()
            ->where('submitted_by_email', $email)
            ->where('submission_type', 'Self-Declaration')
            ->where('position_id', $positionId)
            ->exists()) {
            return redirect()->route('home')->with('candidacy_error', 'You have already submitted candidacy for this position.');
        }

        CandidateSubmission::query()->create([
            'candidate_name' => trim($validated['full_name']),
            'submitted_by_email' => $email,
            'submission_type' => 'Self-Declaration',
            'status' => 'Pending',
            'position_id' => $positionId,
        ]);

        $this->lockActivePosition();

        return redirect()->route('home')->with('submission_success', 'Your candidacy has been recorded successfully.');
    }

    public function submitNomination(Request $request): RedirectResponse
    {
        $positionId = $this->activePositionId();
        $position = $positionId ? ElectionPosition::query()->find($positionId) : null;
        if (! $position || ! $position->nomination_open) {
            return redirect()->route('home')->with('submission_error', 'Nominations are currently closed by the administrator.');
        }

        $validated = $request->validate([
            'nominee_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));
        $isRegistered = RegisteredVoter::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->exists();

        if (! $isRegistered) {
            return back()
                ->withInput()
                ->with('nomination_error', 'You are not eligible to vote. Please contact an election administrator.');
        }

        if (CandidateSubmission::query()
            ->where('submitted_by_email', $email)
            ->where('submission_type', 'Peer Nomination')
            ->where('position_id', $positionId)
            ->exists()) {
            return redirect()->route('home')->with('nomination_error', 'You have already submitted a nomination for this position.');
        }

        CandidateSubmission::query()->create([
            'candidate_name' => trim($validated['nominee_name']),
            'submitted_by_email' => $email,
            'submission_type' => 'Peer Nomination',
            'status' => 'Pending',
            'position_id' => $positionId,
        ]);

        $this->lockActivePosition();

        return redirect()->route('home')->with('submission_success', 'Your nomination has been recorded successfully.');
    }

    public function submitVote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'choices' => ['required', 'array', 'min:1', 'max:10'],
            'choices.*' => ['required', 'string'],
            'position_id' => ['nullable', 'integer', 'exists:election_positions,id'],
        ]);

        $position = ElectionPosition::query()->find(session('current_position_id'))
            ?? ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->first()
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->first();

        if (! $position || ! $position->is_unlocked || $position->is_closed) {
            if ($position?->is_closed) {
                $email = session('verified_email');
                if (is_string($email) && RegisteredVoter::query()->where('email', $email)->where('is_active', true)->exists()) {
                    $hasBallot = ElectionVote::query()
                        ->where('position_id', $position->id)
                        ->where('voter_email', $email)
                        ->exists();

                    if (! $hasBallot) {
                        $this->recordAbstention($position->id, $email);
                    }
                }
            }

            return redirect()->route('ballot')->with('ballot_locked', $position?->is_closed
                ? 'Voting is closed. This ballot can no longer be submitted.'
                : 'Please wait for the administrators to unlock the ballot.');
        }

        session(['pending_vote_choices' => $validated['choices']]);
        if (! empty($validated['position_id'])) {
            session(['current_position_id' => $validated['position_id']]);
        }

        return redirect()->route('review-vote');
    }

    public function voteCountdown(): View
    {
        $position = ElectionPosition::query()->find(session('current_position_id'))
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->first();

        $remainingSeconds = $position?->unlocked_at
            ? min(self::BALLOT_WINDOW_SECONDS, max(0, (int) floor(self::BALLOT_WINDOW_SECONDS - $position->unlocked_at->diffInSeconds(now()))))
            : 0;

        return view('vote-countdown', compact('remainingSeconds'));
    }

    public function autoCloseBallot(): JsonResponse
    {
        $position = ElectionPosition::query()->find(session('current_position_id'));
        if ($position?->is_unlocked && $position->unlocked_at
            && $position->unlocked_at->diffInSeconds(now()) >= self::BALLOT_WINDOW_SECONDS) {
            $this->recordAutomaticAbstentions($position);
            $position->update(['is_unlocked' => false, 'is_closed' => true, 'unlocked_at' => null]);
        }

        return response()->json(['closed' => (bool) $position?->is_closed]);
    }

    public function results(): View
    {
        $positions = ElectionPosition::query()->orderBy('sort_order')->orderBy('id')->get();
        $currentPositionId = session('current_position_id');
        $currentIndex = $positions->search(fn (ElectionPosition $position): bool => $position->id === (int) $currentPositionId);
        $allPositionsComplete = $positions->isNotEmpty() && $positions->every(fn (ElectionPosition $position): bool => $position->is_completed);
        $currentPosition = $allPositionsComplete
            ? $positions->last()
            : ($currentIndex !== false ? $positions->get($currentIndex) : $positions->first());
        if ($currentPosition && ! $currentPosition->is_completed) {
            $this->recordAutomaticAbstentions($currentPosition);
            $currentPosition->update(['is_completed' => true]);
        }
        $nextPosition = $positions->first(fn (ElectionPosition $position): bool => ! $position->is_completed && $position->id !== $currentPosition?->id);
        $results = CandidateSubmission::query()
            ->where('status', '!=', 'Rejected')
            ->when($currentPosition, fn ($query) => $query->where('position_id', $currentPosition->id))
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->orderBy('candidate_name')
            ->get();
        $abstentions = $currentPosition
            ? ElectionVote::query()->where('position_id', $currentPosition->id)->where('is_abstain', true)->distinct('voter_email')->count('voter_email')
            : 0;
        $winners = $currentPosition
            ? $this->selectWinners($results, $currentPosition->seats)
            : collect();

        return view('results', [
            'results' => $results,
            'winner' => $results->firstWhere('votes_count', '>', 0),
            'winners' => $winners,
            'currentPosition' => $currentPosition,
            'nextPosition' => $nextPosition,
            'abstentions' => $abstentions,
        ]);
    }

    /**
     * Accept the final vote only after email verification succeeds.
     */
    public function submitFinalVote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'choices' => ['sometimes', 'array', 'min:1', 'max:10'],
            'choices.*' => ['required', 'string'],
        ]);

        $position = ElectionPosition::query()->find(session('current_position_id'));
        if ($position?->is_unlocked && $position->unlocked_at
            && $position->unlocked_at->diffInSeconds(now()) >= self::BALLOT_WINDOW_SECONDS) {
            $position->update(['is_unlocked' => false, 'is_closed' => true, 'unlocked_at' => null]);
        }
        if ($position && (! $position->is_unlocked || $position->is_closed)) {
            return redirect()->route('ballot')->with('ballot_locked', $position->is_closed
                ? 'Voting is closed. This ballot can no longer be submitted.'
                : 'Please wait for the administrators to unlock the ballot.');
        }

        if (! empty($validated['choices'])) {
            session(['pending_vote_choices' => $validated['choices']]);
        }

        $email = session('verified_email');
        $isRegistered = is_string($email) && RegisteredVoter::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->exists();

        if (! $isRegistered) {
            session()->forget('verified_email');

            return back()->with('email_error', 'Verify your registered email before submitting your vote.');
        }

        $choices = session('pending_vote_choices', []);
        if (! is_array($choices) || $choices === []) {
            return back()->with('email_error', 'Select a candidate on the ballot before submitting your vote.');
        }

        $positionId = (int) (session('current_position_id') ?: $position?->id);
        $claimed = $positionId > 0 && DB::transaction(function () use ($choices, $email, $positionId): bool {
            if (ElectionVote::query()
                ->where('position_id', $positionId)
                ->where('voter_email', $email)
                ->exists()) {
                return false;
            }

            $claimed = VoterPositionBallot::query()->insertOrIgnore([
                'position_id' => $positionId,
                'voter_email' => $email,
                'created_at' => now(),
                'updated_at' => now(),
            ]) === 1;

            if (! $claimed) {
                $hasRecordedVote = ElectionVote::query()
                    ->where('position_id', $positionId)
                    ->where('voter_email', $email)
                    ->exists();

                if ($hasRecordedVote) {
                    return false;
                }

                // A previous interrupted submission may have left only the marker.
                VoterPositionBallot::query()
                    ->where('position_id', $positionId)
                    ->where('voter_email', $email)
                    ->delete();

                $claimed = VoterPositionBallot::query()->insertOrIgnore([
                    'position_id' => $positionId,
                    'voter_email' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]) === 1;
            }

            if (! $claimed) {
                return false;
            }

            $this->recordVoteChoices($choices, $email, $positionId);

            if (! ElectionVote::query()
                ->where('position_id', $positionId)
                ->where('voter_email', $email)
                ->exists()) {
                VoterPositionBallot::query()
                    ->where('position_id', $positionId)
                    ->where('voter_email', $email)
                    ->delete();

                return false;
            }

            return true;
        });
        if (! $claimed) {
            session()->forget(['verified_email', 'pending_vote_choices']);

            return redirect()->route('home')->with('submission_error', 'You have already voted for this position.');
        }
        $this->recordVoteChoices($choices, $email, $positionId > 0 ? $positionId : null);
        $receiptPosition = $positionId > 0 ? ElectionPosition::query()->find($positionId) : null;
        $candidateChoices = collect($choices)
            ->filter(fn (string $choice): bool => str_starts_with($choice, 'candidate-'))
            ->map(fn (string $choice): int => (int) str_replace('candidate-', '', $choice));
        $receiptNames = CandidateSubmission::query()
            ->whereIn('id', $candidateChoices)
            ->pluck('candidate_name')
            ->values();
        session([
            'receipt_position' => $receiptPosition?->name ?? 'Election position',
            'receipt_nominee' => $receiptNames->isNotEmpty() ? $receiptNames->implode(', ') : 'ABSTAIN',
            'vote_submitted_at' => now()->toIso8601String(),
            'receipt_token' => hash('sha256', $email.'|'.$positionId.'|'.now()->format('c').'|'.bin2hex(random_bytes(8))),
        ]);
        session()->forget('pending_vote_choices');

        return redirect()->route('vote-countdown');
    }

    private function recordVoteChoices(array $choices, string $email, ?int $positionId): void
    {
        $normalizedChoices = collect($choices)
            ->filter(fn (string $choice): bool => $choice !== 'abstain')
            ->map(fn (string $choice): string => str_starts_with($choice, 'candidate-') ? str_replace('candidate-', '', $choice) : $choice)
            ->unique()
            ->values();

        if ($normalizedChoices->isEmpty()) {
            if ($positionId && in_array('abstain', $choices, true)) {
                $this->recordAbstention($positionId, $email, false);
            }

            return;
        }

        $candidates = CandidateSubmission::query()
            ->where('status', '!=', 'Rejected')
            ->when($positionId, fn ($query) => $query->where('position_id', $positionId))
            ->where(function ($query) use ($normalizedChoices): void {
                $query->whereIn('id', $normalizedChoices->filter(fn (string $choice): bool => ctype_digit($choice))->map(fn (string $choice): int => (int) $choice))
                    ->orWhereIn('candidate_name', $normalizedChoices);
            })
            ->get();

        foreach ($candidates as $candidate) {
            ElectionVote::query()->create([
                'position_id' => $positionId,
                'candidate_submission_id' => $candidate->id,
                'voter_email' => $email,
                'is_abstain' => false,
            ]);
        }
    }

    private function recordAbstention(int $positionId, string $email, bool $claimBallot = true): void
    {
        if ($claimBallot) {
            $claimed = VoterPositionBallot::query()->insertOrIgnore([
                'position_id' => $positionId,
                'voter_email' => $email,
                'created_at' => now(),
                'updated_at' => now(),
            ]) === 1;
            if (! $claimed) {
                return;
            }
        }

        ElectionVote::query()->firstOrCreate(
            ['position_id' => $positionId, 'voter_email' => $email, 'is_abstain' => true],
            ['candidate_submission_id' => null],
        );
    }

    private function selectWinners($candidates, int $seats)
    {
        $positiveCandidates = $candidates->filter(
            fn (CandidateSubmission $candidate): bool => $candidate->votes_count > 0
        )->values();

        if ($positiveCandidates->isEmpty()) {
            return $positiveCandidates;
        }

        // A tie at the final winning place elects every tied candidate.
        $cutoffCandidate = $positiveCandidates->get(max(0, $seats - 1));
        $cutoffVotes = $cutoffCandidate?->votes_count;

        return $positiveCandidates
            ->filter(fn (CandidateSubmission $candidate): bool => $candidate->votes_count >= $cutoffVotes)
            ->values();
    }

    private function recordAutomaticAbstentions(ElectionPosition $position): void
    {
        $candidateIds = CandidateSubmission::query()->where('position_id', $position->id)->pluck('id');
        $votedEmails = ElectionVote::query()
            ->where(function ($query) use ($position, $candidateIds): void {
                $query->where('position_id', $position->id)->orWhereIn('candidate_submission_id', $candidateIds);
            })
            ->pluck('voter_email')
            ->map(fn (string $email): string => strtolower($email))
            ->unique();

        RegisteredVoter::query()
            ->where('is_active', true)
            ->pluck('email')
            ->map(fn (string $email): string => strtolower($email))
            ->diff($votedEmails)
            ->each(fn (string $email) => ElectionVote::query()->create([
                'position_id' => $position->id,
                'voter_email' => $email,
                'is_abstain' => true,
            ]));
    }

    private function activePositionId(): ?int
    {
        return ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->value('id')
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->value('id');
    }

    private function timelineStep(?ElectionPosition $position, $submissions): int
    {
        if (! $position || $position->is_completed || $position->is_closed) {
            return 5;
        }

        if ($position->is_unlocked) {
            return 4;
        }

        if ($submissions->contains(fn ($submission): bool => $submission->status === 'Pending')) {
            return 2;
        }

        return $submissions->isNotEmpty() ? 3 : 1;
    }

    private function lockActivePosition(): void
    {
        $position = ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->first();

        $position?->update(['is_unlocked' => false, 'is_closed' => false]);
    }
}
