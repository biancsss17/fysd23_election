<?php

namespace Tests\Feature;

use App\Models\RegisteredVoter;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use App\Models\VoterPositionBallot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoterEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unregistered_email_is_rejected(): void
    {
        $response = $this->post(route('review-vote.verify-email'), [
            'email' => 'missing@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('email_error');
        $this->assertGuest();
    }

    public function test_registered_email_unlocks_final_vote_submission(): void
    {
        $position = ElectionPosition::create([
            'name' => 'President',
            'seats' => 1,
            'rule' => 'single',
            'allow_abstain' => true,
            'max_selections' => 1,
            'is_unlocked' => true,
        ]);

        RegisteredVoter::create([
            'email' => 'voter@example.com',
            'is_active' => true,
        ]);
        RegisteredVoter::create([
            'email' => 'other-voter@example.com',
            'is_active' => true,
        ]);

        $response = $this->withSession(['current_position_id' => $position->id])->post(route('review-vote.verify-email'), [
            'email' => 'VOTER@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('verified_email', 'voter@example.com');
        $response->assertSessionHas('email_success');

        $this->withSession(['current_position_id' => $position->id, 'pending_vote_choices' => ['abstain']])
            ->post(route('review-vote.submit'))
            ->assertRedirect(route('vote-countdown'));

        $this->withSession(['current_position_id' => $position->id])
            ->post(route('review-vote.verify-email'), [
                'email' => 'VOTER@example.com',
                'choices' => ['abstain'],
            ])
            ->assertSessionHas('email_error', 'You have already voted for this position.');
    }

    public function test_registered_voter_can_reenter_the_ballot_access_page(): void
    {
        $position = ElectionPosition::create([
            'name' => 'President',
            'seats' => 1,
            'rule' => 'single',
            'allow_abstain' => true,
            'max_selections' => 1,
            'is_unlocked' => true,
        ]);

        RegisteredVoter::create([
            'email' => 'voter@example.com',
            'is_active' => true,
        ]);
        RegisteredVoter::create([
            'email' => 'other-voter@example.com',
            'is_active' => true,
        ]);

        $this->post(route('voter-access.verify'), [
            'email' => 'VOTER@example.com',
        ])->assertRedirect(route('ballot'))
            ->assertSessionHas('voter_access_email', 'voter@example.com');

        $this->get(route('voter-access'))
            ->assertOk()
            ->assertSee('Registered email address')
            ->assertHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->assertDontSee('readonly', false)
            ->assertDontSee('already active in another ballot tab');

        $this->post(route('voter-access.verify'), [
            'email' => 'other-voter@example.com',
        ])->assertRedirect(route('ballot'))
            ->assertSessionHas('verified_email', 'other-voter@example.com');

        $this->post(route('voter-access.verify'), [
            'email' => 'voter@example.com',
        ])->assertRedirect()
            ->assertSessionHas('verified_email', 'voter@example.com');
    }

    public function test_voter_access_waits_until_admin_unlocks_voting(): void
    {
        ElectionPosition::create([
            'name' => 'President',
            'seats' => 1,
            'rule' => 'single',
            'allow_abstain' => true,
            'max_selections' => 1,
            'is_unlocked' => false,
        ]);
        RegisteredVoter::create(['email' => 'waiting@example.com', 'is_active' => true]);

        $this->get(route('voter-access'))
            ->assertOk()
            ->assertSee('Voting locked');
        $this->post(route('voter-access.verify'), ['email' => 'waiting@example.com'])
            ->assertRedirect()
            ->assertSessionHas('access_error', 'Voting has not started yet. Please wait for the administrator to unlock the ballot.');
    }

    public function test_email_with_a_recorded_vote_cannot_reopen_the_same_position(): void
    {
        $position = ElectionPosition::create([
            'name' => 'President',
            'seats' => 1,
            'rule' => 'single',
            'allow_abstain' => true,
            'max_selections' => 1,
            'is_unlocked' => true,
        ]);

        RegisteredVoter::create([
            'email' => 'voter@example.com',
            'is_active' => true,
        ]);
        ElectionVote::create([
            'position_id' => $position->id,
            'voter_email' => 'voter@example.com',
            'is_abstain' => true,
        ]);

        $this->post(route('voter-access.verify'), [
            'email' => 'voter@example.com',
        ])->assertRedirect()
            ->assertSessionHas('access_error', 'This email has already submitted a vote for this position.');
    }

    public function test_email_with_a_recorded_vote_can_enter_the_next_position(): void
    {
        $firstPosition = ElectionPosition::create([
            'name' => 'President', 'seats' => 1, 'rule' => 'single', 'allow_abstain' => true,
            'max_selections' => 1, 'is_unlocked' => true, 'is_completed' => true,
        ]);
        $secondPosition = ElectionPosition::create([
            'name' => 'Secretary', 'seats' => 1, 'rule' => 'single', 'allow_abstain' => true,
            'max_selections' => 1, 'is_unlocked' => true,
        ]);
        RegisteredVoter::create(['email' => 'voter@example.com', 'is_active' => true]);
        ElectionVote::create(['position_id' => $firstPosition->id, 'voter_email' => 'voter@example.com', 'is_abstain' => true]);

        $this->post(route('voter-access.verify'), ['email' => 'voter@example.com'])
            ->assertRedirect(route('ballot'))
            ->assertSessionHas('verified_email', 'voter@example.com');
        $this->get(route('ballot'))->assertOk()->assertSee('Secretary');
    }

    public function test_interrupted_ballot_marker_does_not_block_a_retry(): void
    {
        $position = ElectionPosition::create([
            'name' => 'President', 'seats' => 1, 'rule' => 'single', 'allow_abstain' => true,
            'max_selections' => 1, 'is_unlocked' => true,
        ]);
        RegisteredVoter::create(['email' => 'voter@example.com', 'is_active' => true]);
        VoterPositionBallot::create([
            'position_id' => $position->id,
            'voter_email' => 'voter@example.com',
        ]);

        $this->withSession([
            'current_position_id' => $position->id,
            'verified_email' => 'voter@example.com',
            'pending_vote_choices' => ['abstain'],
        ])->post(route('review-vote.submit'))
            ->assertRedirect(route('vote-countdown'));

        $this->assertDatabaseHas('election_votes', [
            'position_id' => $position->id,
            'voter_email' => 'voter@example.com',
            'is_abstain' => true,
        ]);
    }
}
