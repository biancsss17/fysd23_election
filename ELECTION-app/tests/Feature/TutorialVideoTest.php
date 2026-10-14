<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorialVideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_offered_only_the_user_cut(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertSee('Watch user tutorial')
            ->assertSee('videos/user-tutorial.mp4')
            ->assertDontSee('videos/admin-tutorial.mp4')
            ->assertSee('preload="none"', false)
            ->assertSee('playsinline', false);
    }

    public function test_dashboard_offers_the_full_admin_video(): void
    {
        $admin = AdminUser::create([
            'name' => 'Demo Admin',
            'email' => 'admin@example.com',
            'password' => 'not-a-live-password',
            'is_active' => true,
        ]);
        $this->withSession(['admin_id' => $admin->id])
            ->get(route('admin.dashboard'))->assertOk()
            ->assertSee('Watch admin tutorial')
            ->assertSee('videos/admin-tutorial.mp4')
            ->assertDontSee('videos/user-tutorial.mp4');
    }

    public function test_both_video_assets_are_packaged(): void
    {
        foreach (['admin', 'user'] as $audience) {
            $this->assertFileExists(public_path('videos/'.$audience.'-tutorial.mp4'));
            $this->assertGreaterThan(10000, filesize(public_path('videos/'.$audience.'-tutorial.mp4')));
        }
    }
}
