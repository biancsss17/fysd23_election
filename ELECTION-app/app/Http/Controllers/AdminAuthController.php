<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\AuditLog;
use App\Models\CandidateSubmission;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use App\Models\RegisteredVoter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = AdminUser::query()
            ->where('email', strtolower(trim($validated['email'])))
            ->where('is_active', true)
            ->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('admin_error', 'The administrator email or password is incorrect.');
        }

        $request->session()->regenerate();
        $request->session()->put([
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function dashboard(): Response
    {
        $position = $this->activePosition();
        $registeredVoterCount = RegisteredVoter::query()->where('is_active', true)->count();
        $candidates = $this->positionCandidates($position);
        $ballotsCast = $this->positionBallotsCast($position);
        $abstentions = $position
            ? ElectionVote::query()->where('position_id', $position->id)->where('is_abstain', true)->distinct('voter_email')->count('voter_email')
            : 0;

        return response()
            ->view('admin-dashboard', [
            'registeredVoterCount' => $registeredVoterCount,
            'ballotsCast' => $ballotsCast,
            'participationRate' => $registeredVoterCount > 0
                ? min(100, max(0, round(($ballotsCast / $registeredVoterCount) * 100)))
                : 0,
            'position' => $position,
            'candidates' => $candidates,
            'winner' => $candidates->firstWhere('votes_count', '>', 0),
            'abstentions' => $abstentions,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function resetElection(Request $request): RedirectResponse
    {
        $archiveTitle = trim((string) $request->input('archive_name', 'FYS DISTRICT 23 ELECTION FOR 2027-2030'));
        $archiveTitle = $archiveTitle !== '' ? mb_substr($archiveTitle, 0, 255) : 'FYS DISTRICT 23 ELECTION FOR 2027-2030';

        DB::transaction(function () use ($archiveTitle): void {
            $positions = ElectionPosition::query()->orderBy('sort_order')->orderBy('id')->get();
            $archivedResults = $positions
                ->map(function (ElectionPosition $position): array {
                    $result = $this->buildPositionResult($position);

                    return [
                        'position' => $position->name,
                        'seats' => $position->seats,
                        'ballots_cast' => $result['ballots_cast'],
                        'abstentions' => $result['abstentions'],
                        'winners' => $result['winners']->map(fn (CandidateSubmission $winner): array => [
                            'name' => $winner->display_candidate_name,
                            'votes' => $winner->votes_count,
                        ])->values()->all(),
                    ];
                })
                ->values()
                ->all();

            $snapshot = [
                'positions' => $positions->map(fn (ElectionPosition $position): array => $position->only([
                    'id', 'name', 'sort_order', 'seats', 'rule', 'allow_abstain', 'max_selections', 'is_completed', 'is_unlocked', 'is_closed', 'candidacy_open', 'nomination_open', 'unlocked_at',
                ]))->values()->all(),
                'voters' => RegisteredVoter::query()->get()->map(fn (RegisteredVoter $voter): array => $voter->only(['email', 'is_active']))->values()->all(),
                'candidates' => CandidateSubmission::query()->get()->map(fn (CandidateSubmission $candidate): array => $candidate->only([
                    'id', 'candidate_name', 'display_name', 'submitted_by_email', 'submission_type', 'status', 'is_manual_winner', 'position_id',
                ]))->values()->all(),
                'votes' => ElectionVote::query()->get()->map(fn (ElectionVote $vote): array => $vote->only([
                    'position_id', 'candidate_submission_id', 'voter_email', 'is_abstain',
                ]))->values()->all(),
            ];

            AuditLog::query()->delete();
            if ($positions->isNotEmpty()) {
                AuditLog::query()->create([
                    'title' => $archiveTitle,
                    'action' => 'Election reset archive',
                    'results' => ['positions' => $archivedResults, 'snapshot' => $snapshot],
                    'created_by' => session('admin_email'),
                ]);
            }

            ElectionVote::query()->delete();
            CandidateSubmission::query()->delete();
            ElectionPosition::query()->delete();
            RegisteredVoter::query()->delete();
        });

        return redirect()->route('admin.dashboard')->with('election_reset', 'Election reset successfully.');
    }

    public function importLocalSnapshot(Request $request): RedirectResponse
    {
        $snapshot = json_decode((string) $request->input('snapshot'), true);

        if (! is_array($snapshot)) {
            return back()->with('position_error', 'The local election snapshot is invalid.');
        }

        DB::transaction(function () use ($snapshot): void {
            ElectionVote::query()->delete();
            CandidateSubmission::query()->delete();
            ElectionPosition::query()->delete();
            RegisteredVoter::query()->delete();
            AuditLog::query()->delete();

            $positionIds = [];
            foreach ($snapshot['positions'] ?? [] as $storedPosition) {
                $oldId = $storedPosition['id'];
                unset($storedPosition['id']);
                $positionIds[$oldId] = ElectionPosition::query()->create($storedPosition)->id;
            }

            $candidateIds = [];
            foreach ($snapshot['candidates'] ?? [] as $storedCandidate) {
                $oldId = $storedCandidate['id'];
                $oldPositionId = $storedCandidate['position_id'];
                unset($storedCandidate['id'], $storedCandidate['position_id']);
                $storedCandidate['position_id'] = $positionIds[$oldPositionId] ?? null;
                $candidateIds[$oldId] = CandidateSubmission::query()->create($storedCandidate)->id;
            }

            foreach ($snapshot['voters'] ?? [] as $storedVoter) {
                RegisteredVoter::query()->create($storedVoter);
            }

            foreach ($snapshot['votes'] ?? [] as $storedVote) {
                $storedVote['position_id'] = $positionIds[$storedVote['position_id']] ?? null;
                $storedVote['candidate_submission_id'] = $storedVote['candidate_submission_id'] !== null
                    ? ($candidateIds[$storedVote['candidate_submission_id']] ?? null)
                    : null;
                ElectionVote::query()->create($storedVote);
            }

            foreach ($snapshot['audit'] ?? [] as $storedAudit) {
                unset($storedAudit['id']);
                AuditLog::query()->create($storedAudit);
            }
        });

        return redirect()->route('admin.dashboard')->with('position_success', 'Local election snapshot imported successfully.');
    }

    public function restoreAuditLog(AuditLog $auditLog): RedirectResponse
    {
        $snapshot = $auditLog->results['snapshot'] ?? null;

        if (! is_array($snapshot)) {
            return back()->with('audit_error', 'This audit archive does not contain a restorable election snapshot.');
        }

        DB::transaction(function () use ($snapshot): void {
            ElectionVote::query()->delete();
            CandidateSubmission::query()->delete();
            ElectionPosition::query()->delete();
            RegisteredVoter::query()->delete();

            $positionIds = [];
            foreach ($snapshot['positions'] ?? [] as $storedPosition) {
                $oldId = $storedPosition['id'];
                unset($storedPosition['id']);
                $positionIds[$oldId] = ElectionPosition::query()->create($storedPosition)->id;
            }

            $candidateIds = [];
            foreach ($snapshot['candidates'] ?? [] as $storedCandidate) {
                $oldId = $storedCandidate['id'];
                $oldPositionId = $storedCandidate['position_id'];
                unset($storedCandidate['id'], $storedCandidate['position_id']);
                $storedCandidate['position_id'] = $positionIds[$oldPositionId] ?? null;
                $candidateIds[$oldId] = CandidateSubmission::query()->create($storedCandidate)->id;
            }

            foreach ($snapshot['voters'] ?? [] as $storedVoter) {
                RegisteredVoter::query()->create($storedVoter);
            }

            foreach ($snapshot['votes'] ?? [] as $storedVote) {
                $storedVote['position_id'] = $positionIds[$storedVote['position_id']] ?? null;
                $storedVote['candidate_submission_id'] = $storedVote['candidate_submission_id'] !== null
                    ? ($candidateIds[$storedVote['candidate_submission_id']] ?? null)
                    : null;
                ElectionVote::query()->create($storedVote);
            }
        });

        return redirect()->route('admin.dashboard')->with('election_restored', 'The archived election was restored successfully.');
    }

    public function auditLog(): View
    {
        // Clean up older/empty duplicate rows left by previous reset attempts.
        $auditLogs = AuditLog::query()->latest()->get();
        $archive = $auditLogs->first(function (AuditLog $auditLog): bool {
            return count($auditLog->results['snapshot']['positions'] ?? []) > 0;
        }) ?? $auditLogs->first();
        $auditLogs->reject(fn (AuditLog $auditLog): bool => $auditLog->is($archive))->each->delete();

        return view('admin-audit-log', [
            'auditLogs' => $archive ? collect([$archive]) : collect(),
        ]);
    }

    public function auditLogShow(AuditLog $auditLog): View
    {
        return view('admin-audit-log-show', compact('auditLog'));
    }

    public function voterManagement(): View
    {
        $votedEmails = ElectionVote::query()
            ->distinct()
            ->pluck('voter_email')
            ->map(fn (string $email): string => strtolower(trim($email)))
            ->unique()
            ->values();

        return view('admin-voter-management', [
            'voters' => RegisteredVoter::query()->orderBy('email')->get(),
            'votedEmails' => $votedEmails,
        ]);
    }

    public function voterManagementData(): JsonResponse
    {
        $voters = RegisteredVoter::query()->orderBy('email')->get();

        return response()->json([
            'count' => $voters->count(),
            'latest_updated_at' => $voters->max('updated_at'),
        ]);
    }

    public function positionManagement(): View
    {
        $positions = ElectionPosition::query()->orderBy('sort_order')->orderBy('id')->get();
        $sequence = ['President', 'Vice President', 'Secretary', 'Assist Sec', 'Treasurer', 'Auditor', 'Board of Directors', 'Inspector'];
        $existingNames = $positions->pluck('name')->map(fn (string $name): string => strtolower(trim($name)));
        $nextPositionName = collect($sequence)
            ->first(fn (string $name): bool => ! $existingNames->contains(strtolower($name)), '');

        return view('admin-position-management', [
            'positions' => $positions,
            'nextPositionName' => $nextPositionName,
        ]);
    }

    public function resultsDocumentPreview(): View
    {
        $registeredVoterCount = RegisteredVoter::query()->where('is_active', true)->count();
        $positions = ElectionPosition::query()
            ->orderBy('sort_order')->orderBy('id')
            ->get()
            ->map(fn (ElectionPosition $position): array => $this->buildPositionResult($position))
            ->filter(fn (array $result): bool => $result['candidates']->isNotEmpty() || $result['ballots_cast'] > 0 || $result['abstentions'] > 0)
            ->values();
        $ballotsCast = $positions->sum('ballots_cast');

        return view('admin-results-document-preview', [
            'positions' => $positions,
            'registeredVoterCount' => $registeredVoterCount,
            'ballotsCast' => $ballotsCast,
            'participationRate' => $registeredVoterCount > 0
                ? min(100, max(0, round(($ballotsCast / $registeredVoterCount) * 100)))
                : 0,
        ]);
    }

    public function updateWinnerName(Request $request, CandidateSubmission $candidateSubmission): JsonResponse
    {
        $validated = $request->validate([
            'candidate_name' => ['nullable', 'string', 'max:255'],
        ]);

        $candidateSubmission->update([
            'display_name' => trim((string) ($validated['candidate_name'] ?? '')),
            ...((trim((string) ($validated['candidate_name'] ?? '')) !== '') ? ['candidate_name' => trim($validated['candidate_name'])] : []),
        ]);

        return response()->json(['saved' => true, 'candidate_name' => $candidateSubmission->display_candidate_name]);
    }

    public function addManualWinner(Request $request, ElectionPosition $position): JsonResponse
    {
        $validated = $request->validate([
            'candidate_name' => ['required', 'string', 'max:255'],
        ]);

        $candidate = CandidateSubmission::query()->create([
            'candidate_name' => trim($validated['candidate_name']),
            'submitted_by_email' => session('admin_email'),
            'submission_type' => 'Manual winner',
            'status' => 'Approved',
            'is_manual_winner' => true,
            'position_id' => $position->id,
        ]);

        return response()->json(['saved' => true, 'candidate_id' => $candidate->id, 'candidate_name' => $candidate->candidate_name]);
    }

    public function addManualPosition(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'position_name' => ['required', 'string', 'max:255'],
            'candidate_name' => ['required', 'string', 'max:255'],
        ]);

        $position = ElectionPosition::query()->create([
            'name' => trim($validated['position_name']),
            'sort_order' => ((int) ElectionPosition::query()->max('sort_order')) + 1,
            'seats' => 1,
            'rule' => 'single',
            'allow_abstain' => true,
            'max_selections' => 1,
            'is_completed' => true,
            'is_unlocked' => false,
            'is_closed' => true,
        ]);

        $candidate = CandidateSubmission::query()->create([
            'candidate_name' => trim($validated['candidate_name']),
            'submitted_by_email' => session('admin_email') ?: 'admin',
            'submission_type' => 'Manual winner',
            'status' => 'Approved',
            'is_manual_winner' => true,
            'position_id' => $position->id,
        ]);

        return response()->json([
            'saved' => true,
            'position_id' => $position->id,
            'position_name' => $position->name,
            'candidate_id' => $candidate->id,
            'candidate_name' => $candidate->candidate_name,
        ]);
    }

    public function storePosition(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position_name' => ['required', 'string', 'max:255'],
            'seats' => ['required', 'integer', 'min:1', 'max:10'],
            'allow_abstain' => ['nullable', 'boolean'],
            'max_selections' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $seats = (int) $validated['seats'];
        $rule = $seats === 1 ? 'single' : 'multi';
        $maxSelections = (int) $validated['max_selections'];

        ElectionPosition::query()->create([
            'name' => trim($validated['position_name']),
            'sort_order' => ((int) ElectionPosition::query()->max('sort_order')) + 1,
            'seats' => $seats,
            'rule' => $rule,
            'allow_abstain' => (bool) ($validated['allow_abstain'] ?? false),
            'max_selections' => $maxSelections,
        ]);

        return redirect()->route('admin.position-management')->with('position_success', 'Position created successfully.');
    }

    public function reorderPosition(Request $request, ElectionPosition $position): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => ['nullable', 'in:up,down'],
            'target_position_id' => ['nullable', 'integer', 'exists:election_positions,id'],
        ]);

        $target = ! empty($validated['target_position_id'])
            ? ElectionPosition::query()->find($validated['target_position_id'])
            : null;

        $positions = ElectionPosition::query()
            ->orderBy('sort_order')->orderBy('id')->get();
        if (! $target && ! empty($validated['direction'])) {
            $index = $positions->search(fn (ElectionPosition $item): bool => $item->id === $position->id);
            $targetIndex = $validated['direction'] === 'up' ? $index - 1 : $index + 1;
            $target = $index !== false ? $positions->get($targetIndex) : null;
        }

        if (! $target || $target->id === $position->id) {
            return back()->with('position_error', 'Choose a different position to swap.');
        }

        DB::transaction(function () use ($position, $target): void {
            $currentOrder = $position->sort_order;
            $position->update(['sort_order' => $target->sort_order]);
            $target->update(['sort_order' => $currentOrder]);
        });

        return back()->with('position_success', 'Position order updated successfully.');
    }

    public function deletePosition(ElectionPosition $position): RedirectResponse
    {
        if ($position->is_completed) {
            return back()->with('position_error', 'Completed positions cannot be erased.');
        }

        DB::transaction(function () use ($position): void {
            $candidateIds = CandidateSubmission::query()
                ->where('position_id', $position->id)
                ->pluck('id');

            ElectionVote::query()
                ->where('position_id', $position->id)
                ->orWhereIn('candidate_submission_id', $candidateIds)
                ->delete();

            CandidateSubmission::query()
                ->where('position_id', $position->id)
                ->delete();

            $position->delete();
        });

        return redirect()->route('admin.position-management')->with('position_success', "{$position->name} position erased successfully.");
    }

    public function unlockPosition(ElectionPosition $position): RedirectResponse
    {
        $activePosition = $this->activePosition();

        if (! $activePosition || $activePosition->id !== $position->id) {
            return back()->with('position_error', 'Only the current position can be unlocked.');
        }

        $position->update(['is_unlocked' => true, 'is_closed' => false, 'unlocked_at' => now()]);

        return redirect()->route('admin.dashboard')->with('position_success', "{$position->name} ballot unlocked successfully.");
    }

    public function closePosition(ElectionPosition $position): RedirectResponse
    {
        $activePosition = $this->activePosition();

        if (! $activePosition || $activePosition->id !== $position->id) {
            return back()->with('position_error', 'Only the current position can be closed.');
        }

        $this->recordAutomaticAbstentions($position);
        $position->update(['is_closed' => true, 'is_unlocked' => false, 'unlocked_at' => null]);

        return back()->with('position_success', "{$position->name} voting has been closed.");
    }

    public function reopenPosition(ElectionPosition $position): RedirectResponse
    {
        $activePosition = $this->activePosition();

        if (! $activePosition || $activePosition->id !== $position->id) {
            return back()->with('position_error', 'Only the current position can be reopened.');
        }

        if ($position->is_completed) {
            return back()->with('position_error', 'Completed positions cannot be reopened.');
        }

        $position->update(['is_closed' => false, 'is_unlocked' => true, 'unlocked_at' => now()]);

        return back()->with('position_success', "{$position->name} voting reopened successfully.");
    }

    public function lockPosition(ElectionPosition $position): RedirectResponse
    {
        $activePosition = $this->activePosition();

        if (! $activePosition || $activePosition->id !== $position->id) {
            return back()->with('position_error', 'Only the current position can be locked.');
        }

        $position->update(['is_unlocked' => false, 'is_closed' => false, 'unlocked_at' => null]);

        return back()->with('position_success', "{$position->name} ballot locked successfully.");
    }

    public function toggleCandidacy(ElectionPosition $position): RedirectResponse
    {
        if ($position->is_completed || $position->is_closed) {
            return back()->with('position_error', 'Completed or closed positions cannot accept candidacy submissions.');
        }

        $position->update(['candidacy_open' => ! $position->candidacy_open]);

        return back()->with('position_success', $position->candidacy_open
            ? "{$position->name} candidacy is now open."
            : "{$position->name} candidacy is now closed.");
    }

    public function toggleNomination(ElectionPosition $position): RedirectResponse
    {
        if ($position->is_completed || $position->is_closed) {
            return back()->with('position_error', 'Completed or closed positions cannot accept nominations.');
        }

        $position->update(['nomination_open' => ! $position->nomination_open]);

        return back()->with('position_success', $position->nomination_open
            ? "{$position->name} nominations are now open."
            : "{$position->name} nominations are now closed.");
    }

    public function storeVoter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));

        if (RegisteredVoter::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            return back()->withInput()->with('voter_error', 'That email is already registered.');
        }

        RegisteredVoter::query()->create([
            'email' => $email,
            'is_active' => true,
        ]);

        return back()->with('voter_success', 'Voter email added successfully.');
    }

    public function updateVoterEligibility(Request $request, RegisteredVoter $voter): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $voter->update([
            'is_active' => (bool) $validated['is_active'],
        ]);

        return back()->with('voter_success', 'Voter eligibility updated successfully.');
    }

    public function importVoters(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'emails' => ['required', 'string', 'max:100000'],
        ]);

        $emails = preg_split('/[\r\n,]+/', $validated['emails'], -1, PREG_SPLIT_NO_EMPTY);
        $emails = array_values(array_unique(array_map(
            static fn (string $email): string => strtolower(trim($email)),
            $emails
        )));

        foreach ($emails as $email) {
            if (! Validator::make(['email' => $email], ['email' => ['required', 'email', 'max:255']])->passes()) {
                return back()->withInput()->with('voter_import_error', "Invalid voter email: {$email}");
            }
        }

        DB::transaction(function () use ($emails): void {
            RegisteredVoter::query()->whereNotIn('email', $emails)->delete();

            foreach ($emails as $email) {
                RegisteredVoter::query()->updateOrCreate(
                    ['email' => $email],
                    ['is_active' => true]
                );
            }
        });

        return back()->with('voter_success', 'Voter roster imported successfully.');
    }

    public function deleteVoter(RegisteredVoter $voter): RedirectResponse
    {
        $voter->delete();

        return back()->with('voter_success', 'Voter email removed successfully.');
    }

    public function deleteVoterByEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delete_email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($validated['delete_email']));
        $voter = RegisteredVoter::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $voter) {
            return back()->withInput()->with('voter_import_error', 'That voter email was not found.');
        }

        $voter->delete();

        return back()->with('voter_success', 'Voter email removed successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['admin_id', 'admin_name', 'admin_email']);
        $request->session()->regenerateToken();

        return redirect()->route('splash');
    }

    private function activePosition(): ?ElectionPosition
    {
        return ElectionPosition::query()->where('is_completed', false)->where('is_closed', false)->orderBy('sort_order')->orderBy('id')->first()
            ?? ElectionPosition::query()->where('is_completed', false)->orderBy('sort_order')->orderBy('id')->first()
            ?? ElectionPosition::query()->orderBy('sort_order')->orderBy('id')->first();
    }

    private function positionCandidates(?ElectionPosition $position)
    {
        return CandidateSubmission::query()
            ->where('status', '!=', 'Rejected')
            ->when($position, fn ($query) => $query->where('position_id', $position->id))
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->orderBy('candidate_name')
            ->get();
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

    private function positionBallotsCast(?ElectionPosition $position): int
    {
        if (! $position) {
            return 0;
        }

        $candidateIds = CandidateSubmission::query()->where('position_id', $position->id)->pluck('id');

        return ElectionVote::query()
            ->where(function ($query) use ($position, $candidateIds): void {
                $query->where('position_id', $position->id)->orWhereIn('candidate_submission_id', $candidateIds);
            })
            ->distinct('voter_email')
            ->count('voter_email');
    }

    private function buildPositionResult(ElectionPosition $position): array
    {
        $candidates = $this->positionCandidates($position);
        $winners = $this->selectWinners($candidates, $position->seats);

        return [
            'position' => $position,
            'candidates' => $candidates,
            'winners' => $winners,
            'ballots_cast' => $this->positionBallotsCast($position),
            'abstentions' => ElectionVote::query()->where('position_id', $position->id)->where('is_abstain', true)->distinct('voter_email')->count('voter_email'),
            'is_final' => $position->is_completed || $winners->isNotEmpty(),
        ];
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
}
