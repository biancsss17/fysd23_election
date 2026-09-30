<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\ElectionPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_authentication(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_voter_link_goes_to_splash(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('href="'.route('splash').'"', false)
            ->assertSee('Go to voter page');
    }

    public function test_admin_can_sign_in_and_access_dashboard(): void
    {
        AdminUser::create([
            'name' => 'Election Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
            'is_active' => true,
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('District 23 FYS Election Overview');
    }

    public function test_position_configuration_is_normalized_to_the_selected_rule(): void
    {
        $admin = AdminUser::create([
            'name' => 'Election Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
            'is_active' => true,
        ]);

        $this->withSession([
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
        ])->post(route('admin.position-management.store'), [
            'position_name' => 'President',
            'seats' => 2,
            'rule' => 'multi',
            'allow_abstain' => '1',
            'max_selections' => 5,
        ])->assertRedirect(route('admin.position-management'));

        $position = ElectionPosition::query()->firstOrFail();
        $this->assertSame(2, $position->seats);
        $this->assertSame('multi', $position->rule);
        $this->assertSame(5, $position->max_selections);
    }

    public function test_next_position_name_uses_the_first_missing_official_position(): void
    {
        $admin = AdminUser::create([
            'name' => 'Election Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
            'is_active' => true,
        ]);

        ElectionPosition::create([
            'name' => 'President',
            'seats' => 1,
            'rule' => 'single',
            'allow_abstain' => true,
            'max_selections' => 1,
        ]);

        $this->withSession(['admin_id' => $admin->id])
            ->get(route('admin.position-management'))
            ->assertOk()
            ->assertSee('value="Vice President"', false);
    }
}
