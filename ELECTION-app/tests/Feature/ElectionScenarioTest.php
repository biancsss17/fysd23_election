<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\AuditLog;
use App\Models\CandidateSubmission;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use App\Models\RegisteredVoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ElectionScenarioTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::create([
            'name' => 'Scenario Administrator',
            'email' => 'scenario-admin@example.test',
            'password' => Hash::make('scenario-password'),
            'is_active' => true,
        ]);

        foreach (range(1, 6) as $number) {
            RegisteredVoter::create([
                'email' => "scenario-voter-{$number}@example.test",
                'is_active' => true,
            ]);
        }
    }

    private function adminSession(): array
    {
        return ['admin_id' => $this->admin->id, 'admin_email' => $this->admin->email];
    }

    private function position(string $name, int $seats = 1, string $rule = 'single'): ElectionPosition
    {
        return ElectionPosition::create([
            'name' => $name,
            'seats' => $seats,
            'rule' => $rule,
            'allow_abstain' => true,
            'max_selections' => $rule === 'multi' ? min($seats, 2) : 1,
            'is_completed' => false,
            'is_unlocked' => false,
            'is_closed' => false,
            'candidacy_open' => true,
            'nomination_open' => true,
        ]);
    }

    public function test_empty_election_locks_public_actions(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Election position');
        $this->get(route('voter-access'))->assertOk();
        $this->get(route('ballot'))
            ->assertOk()
            ->assertSee('No candidates are registered for this position yet.')
            ->assertDontSee('Elena Vance')
            ->assertDontSee('Marcus Sterling')
            ->assertDontSee('Sofia Al-Hassan');
        $this->assertDatabaseCount('election_positions', 0);
    }

    public function test_six_positions_and_six_disposable_voters_are_supported(): void
    {
        foreach (['President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor', 'Assist Secretary'] as $name) {
            $this->position($name);
        }

        $this->withSession($this->adminSession())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Eligible Voters');

        $this->assertDatabaseCount('election_positions', 6);
        $this->assertDatabaseCount('registered_voters', 6);
    }

    public function test_admin_can_add_a_voter_without_a_server_error(): void
    {
        $this->withSession($this->adminSession())
            ->post(route('admin.voter-management.store'), [
                'email' => 'new-voter@example.test',
            ])
            ->assertRedirect()
            ->assertSessionHas('voter_success');

        $this->assertDatabaseHas('registered_voters', [
            'email' => 'new-voter@example.test',
            'is_active' => true,
        ]);
    }

    public function test_marking_a_voter_ineligible_revokes_their_recorded_votes(): void
    {
        $position = $this->position('President');
        ElectionVote::create([
            'position_id' => $position->id,
            'voter_email' => 'scenario-voter-1@example.test',
            'is_abstain' => true,
        ]);

        $this->withSession($this->adminSession())
            ->patch(route('admin.voter-management.eligibility', RegisteredVoter::where('email', 'scenario-voter-1@example.test')->firstOrFail()), [
                'is_active' => 0,
            ])
            ->assertRedirect()
            ->assertSessionHas('voter_success', 'Voter marked ineligible and 1 vote record(s) revoked.');

        $this->assertDatabaseHas('registered_voters', [
            'email' => 'scenario-voter-1@example.test',
            'is_active' => false,
        ]);
        $this->assertDatabaseMissing('election_votes', [
            'voter_email' => 'scenario-voter-1@example.test',
        ]);
    }

    public function test_seat_count_and_maximum_selections_are_independent(): void
    {
        $this->withSession($this->adminSession())
            ->post(route('admin.position-management.store'), [
                'position_name' => 'President',
                'seats' => 3,
                'allow_abstain' => 1,
                'max_selections' => 5,
            ])
            ->assertRedirect(route('admin.position-management'));

        $single = ElectionPosition::query()->firstOrFail();
        $this->assertSame(3, $single->seats);
        $this->assertSame('multi', $single->rule);
        $this->assertSame(5, $single->max_selections);

        $this->withSession($this->adminSession())
            ->post(route('admin.position-management.store'), [
                'position_name' => 'Vice President',
                'seats' => 2,
                'allow_abstain' => 1,
                'max_selections' => 5,
            ]);

        $multi = ElectionPosition::query()->where('name', 'Vice President')->firstOrFail();
        $this->assertSame(2, $multi->seats);
        $this->assertSame('multi', $multi->rule);
        $this->assertSame(5, $multi->max_selections);
    }

    public function test_admin_can_switch_unfinished_position_order(): void
    {
        $president = $this->position('President');
        $vicePresident = $this->position('Vice President');

        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.reorder', $vicePresident), ['direction' => 'up'])
            ->assertRedirect();

        $orderedNames = ElectionPosition::query()
            ->orderBy('sort_order')->orderBy('id')->pluck('name')->all();
        $this->assertSame(['Vice President', 'President'], $orderedNames);

        $president->update(['is_completed' => true]);
        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.reorder', $vicePresident), ['target_position_id' => $president->id])
            ->assertRedirect();

        $this->assertSame(['President', 'Vice President'], ElectionPosition::query()
            ->orderBy('sort_order')->orderBy('id')->pluck('name')->all());
    }

    public function test_candidacy_and_nomination_submissions_work_without_admin_review_page(): void
    {
        $position = $this->position('President');

        $this->post(route('candidacy.submit'), [
            'full_name' => 'Scenario Candidate One',
            'email' => 'scenario-voter-1@example.test',
        ])->assertRedirect(route('home'));

        $nomination = $this->post(route('nomination.submit'), [
            'nominee_name' => 'Scenario Candidate Two',
            'email' => 'scenario-voter-2@example.test',
        ])->assertRedirect(route('home'));

        $candidate = CandidateSubmission::query()->where('candidate_name', 'Scenario Candidate One')->firstOrFail();
        $this->assertDatabaseHas('candidate_submissions', ['candidate_name' => 'Scenario Candidate Two', 'status' => 'Pending']);
        $this->assertSame($position->id, $candidate->fresh()->position_id);
        $this->assertNotNull($nomination);

        $this->post(route('candidacy.submit'), [
            'full_name' => 'Duplicate Candidate',
            'email' => 'scenario-voter-1@example.test',
        ])->assertRedirect(route('home'))
            ->assertSessionHas('candidacy_error');

        $this->post(route('nomination.submit'), [
            'nominee_name' => 'Duplicate Nominee',
            'email' => 'scenario-voter-2@example.test',
        ])->assertRedirect(route('home'))
            ->assertSessionHas('nomination_error');
    }

    public function test_unlock_lock_and_manual_close_control_the_current_position(): void
    {
        $position = $this->position('President');

        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.unlock', $position))
            ->assertRedirect(route('admin.dashboard'));
        $this->assertTrue($position->fresh()->is_unlocked);

        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.lock', $position))
            ->assertRedirect();
        $this->assertFalse($position->fresh()->is_unlocked);

        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.unlock', $position));
        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.close', $position))
            ->assertRedirect();
        $this->assertTrue($position->fresh()->is_closed);
        $this->assertSame(6, ElectionVote::query()->where('position_id', $position->id)->where('is_abstain', true)->count());

        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.reopen', $position))
            ->assertRedirect();
        $this->assertFalse($position->fresh()->is_closed);
        $this->assertTrue($position->fresh()->is_unlocked);
    }

    public function test_admin_can_start_candidacy_and_nomination_independently(): void
    {
        $position = $this->position('President');
        $position->update(['candidacy_open' => false, 'nomination_open' => false]);

        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.candidacy', $position))
            ->assertRedirect()
            ->assertSessionHas('position_success', 'President candidacy is now open.');
        $this->assertTrue($position->fresh()->candidacy_open);
        $this->assertFalse($position->fresh()->nomination_open);

        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.nomination', $position))
            ->assertRedirect()
            ->assertSessionHas('position_success', 'President nominations are now open.');
        $this->assertTrue($position->fresh()->nomination_open);
    }

    public function test_closed_unfinished_position_does_not_block_locking_the_next_position(): void
    {
        $closed = $this->position('Treasurer');
        $closed->update(['is_closed' => true]);
        $next = $this->position('Auditor');

        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.lock', $next))
            ->assertRedirect()
            ->assertSessionHas('position_success', 'Auditor ballot locked successfully.');

        $this->assertFalse($next->fresh()->is_unlocked);
    }

    public function test_voter_can_verify_and_submit_a_single_seat_vote(): void
    {
        $position = $this->position('President');
        $position->update(['is_unlocked' => true]);
        $candidate = CandidateSubmission::create([
            'candidate_name' => 'Scenario Winner',
            'submitted_by_email' => 'scenario-voter-1@example.test',
            'submission_type' => 'Self-Declaration',
            'status' => 'Approved',
            'position_id' => $position->id,
        ]);

        $session = $this->withSession(['current_position_id' => $position->id]);
        $session->post(route('vote.submit'), ['choices' => ["candidate-{$candidate->id}"], 'position_id' => $position->id])
            ->assertRedirect(route('review-vote'));
        $session->post(route('review-vote.verify-email'), [
            'email' => 'scenario-voter-1@example.test',
            'choices' => ["candidate-{$candidate->id}"],
        ])->assertRedirect('/');
        $session->post(route('review-vote.submit'))->assertRedirect(route('vote-countdown'));

        $this->assertDatabaseHas('election_votes', [
            'position_id' => $position->id,
            'candidate_submission_id' => $candidate->id,
            'voter_email' => 'scenario-voter-1@example.test',
            'is_abstain' => false,
        ]);

        $this->get(route('vote-receipt'))
            ->assertOk()
            ->assertSee('President')
            ->assertSee('Scenario Winner')
            ->assertDontSee('Elena Vance');
    }

    public function test_abstain_and_automatic_abstentions_are_recorded_once(): void
    {
        $position = $this->position('Secretary');
        $position->update(['is_unlocked' => true]);
        $session = $this->withSession(['current_position_id' => $position->id, 'verified_email' => 'scenario-voter-1@example.test']);

        $session->post(route('review-vote.submit'), ['choices' => ['abstain']])->assertRedirect(route('vote-countdown'));
        $this->assertTrue($position->fresh()->is_unlocked);
        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.close', $position))
            ->assertRedirect();

        $this->assertSame(6, ElectionVote::query()->where('position_id', $position->id)->where('is_abstain', true)->distinct('voter_email')->count('voter_email'));
    }

    public function test_thirty_second_timeout_closes_ballot_and_opens_results(): void
    {
        $position = $this->position('President');
        $this->withSession($this->adminSession())
            ->patch(route('admin.position-management.unlock', $position))
            ->assertRedirect();

        $position->update(['unlocked_at' => Carbon::now()->subSeconds(60)]);

        $this->withSession(['current_position_id' => $position->id])
            ->post(route('vote-countdown.close'))
            ->assertOk()
            ->assertJson(['closed' => true]);

        $this->assertTrue($position->fresh()->is_closed);
        $this->assertSame(6, ElectionVote::query()
            ->where('position_id', $position->id)
            ->where('is_abstain', true)
            ->distinct('voter_email')
            ->count('voter_email'));

        $this->withSession(['current_position_id' => $position->id])
            ->get(route('results'))
            ->assertOk();
        $this->assertTrue($position->fresh()->is_completed);
    }

    public function test_completed_election_results_stay_on_the_last_position_without_session_state(): void
    {
        $firstPosition = $this->position('President');
        $firstPosition->update(['is_completed' => true]);
        $lastPosition = $this->position('Secretary');
        $lastPosition->update(['is_completed' => true]);

        $this->withSession(['current_position_id' => $firstPosition->id])
            ->get(route('results'))
            ->assertOk()
            ->assertSee('Secretary Results')
            ->assertDontSee('President Results');
    }

    public function test_multi_seat_results_only_fill_seats_with_positive_votes(): void
    {
        $position = $this->position('Vice President', 2, 'multi');
        $position->update(['is_unlocked' => true]);
        $candidate = CandidateSubmission::create([
            'candidate_name' => 'Only Winner',
            'submitted_by_email' => 'scenario-voter-1@example.test',
            'submission_type' => 'Self-Declaration',
            'status' => 'Approved',
            'position_id' => $position->id,
        ]);
        ElectionVote::create([
            'position_id' => $position->id,
            'candidate_submission_id' => $candidate->id,
            'voter_email' => 'scenario-voter-1@example.test',
            'is_abstain' => false,
        ]);

        $response = $this->withSession(['current_position_id' => $position->id])->get(route('results'));
        $response->assertOk()->assertSee('Only Winner')->assertSee('Elected winner');
        $this->assertTrue($position->fresh()->is_completed);
    }

    public function test_single_seat_tie_elects_all_candidates_with_the_highest_vote_count(): void
    {
        $position = $this->position('Secretary');
        $firstCandidate = CandidateSubmission::create([
            'candidate_name' => 'Tied Candidate One',
            'submitted_by_email' => 'scenario-voter-1@example.test',
            'submission_type' => 'Self-Declaration',
            'status' => 'Approved',
            'position_id' => $position->id,
        ]);
        $secondCandidate = CandidateSubmission::create([
            'candidate_name' => 'Tied Candidate Two',
            'submitted_by_email' => 'scenario-voter-2@example.test',
            'submission_type' => 'Self-Declaration',
            'status' => 'Approved',
            'position_id' => $position->id,
        ]);

        ElectionVote::create([
            'position_id' => $position->id,
            'candidate_submission_id' => $firstCandidate->id,
            'voter_email' => 'scenario-voter-1@example.test',
            'is_abstain' => false,
        ]);
        ElectionVote::create([
            'position_id' => $position->id,
            'candidate_submission_id' => $secondCandidate->id,
            'voter_email' => 'scenario-voter-2@example.test',
            'is_abstain' => false,
        ]);

        $this->withSession(['current_position_id' => $position->id])
            ->get(route('results'))
            ->assertOk()
            ->assertSee('Tied Candidate One')
            ->assertSee('Tied Candidate Two')
            ->assertSee('Elected winner 1')
            ->assertSee('Elected winner 2');
    }

    public function test_no_votes_and_no_abstentions_has_no_winner(): void
    {
        $position = $this->position('Treasurer');
        $response = $this->withSession(['current_position_id' => $position->id])->get(route('results'));

        $response->assertOk()->assertSee('No counted votes yet.');
        $this->assertSame(6, ElectionVote::query()->where('position_id', $position->id)->where('is_abstain', true)->count());
    }

    public function test_reset_archive_and_restore_round_trip_preserves_scenario_data(): void
    {
        $position = $this->position('Auditor', 2, 'multi');
        $candidate = CandidateSubmission::create([
            'candidate_name' => 'Archived Winner',
            'submitted_by_email' => 'scenario-voter-1@example.test',
            'submission_type' => 'Manual winner',
            'status' => 'Approved',
            'position_id' => $position->id,
        ]);
        ElectionVote::create([
            'position_id' => $position->id,
            'candidate_submission_id' => $candidate->id,
            'voter_email' => 'scenario-voter-1@example.test',
            'is_abstain' => false,
        ]);

        $this->withSession($this->adminSession())
            ->post(route('admin.dashboard.reset-election'), ['archive_name' => 'Disposable Scenario Archive'])
            ->assertRedirect(route('admin.dashboard'));
        $archive = AuditLog::query()->firstOrFail();
        $this->assertSame('Disposable Scenario Archive', $archive->title);
        $this->assertDatabaseCount('election_positions', 0);

        $this->withSession($this->adminSession())
            ->post(route('admin.audit-log.restore', $archive))
            ->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseHas('election_positions', ['name' => 'Auditor', 'seats' => 2]);
        $this->assertDatabaseHas('candidate_submissions', ['candidate_name' => 'Archived Winner']);
        $this->assertDatabaseHas('election_votes', ['voter_email' => 'scenario-voter-1@example.test']);
    }

    public function test_admin_can_edit_winner_and_add_missing_winner_without_exceeding_limits(): void
    {
        $position = $this->position('Assist Secretary');
        $candidate = CandidateSubmission::create([
            'candidate_name' => 'Old Winner Name',
            'submitted_by_email' => $this->admin->email,
            'submission_type' => 'Manual winner',
            'status' => 'Approved',
            'position_id' => $position->id,
        ]);

        $this->withSession($this->adminSession())
            ->patch(route('admin.results-document-preview.winner.update', $candidate), ['candidate_name' => 'Edited Winner Name'])
            ->assertOk()
            ->assertJson(['saved' => true, 'candidate_name' => 'Edited Winner Name']);

        $this->withSession($this->adminSession())
            ->post(route('admin.results-document-preview.winner.add', $position), ['candidate_name' => 'Added Winner Name'])
            ->assertOk()
            ->assertJsonPath('saved', true);

        $this->assertDatabaseHas('candidate_submissions', ['candidate_name' => 'Edited Winner Name']);
        $this->assertDatabaseHas('candidate_submissions', ['candidate_name' => 'Added Winner Name']);
    }
}
