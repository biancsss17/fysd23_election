<?php

namespace Tests\Feature;

use App\Models\RegisteredVoter;
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
        RegisteredVoter::create([
            'email' => 'voter@example.com',
            'is_active' => true,
        ]);

        $response = $this->post(route('review-vote.verify-email'), [
            'email' => 'VOTER@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('verified_email', 'voter@example.com');
        $response->assertSessionHas('email_success');

        $this->withSession(['pending_vote_choices' => ['abstain']])
            ->post(route('review-vote.submit'))
            ->assertRedirect(route('vote-countdown'));
    }
}
