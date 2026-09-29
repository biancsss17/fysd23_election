<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\ElectionPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['email' => strtolower((string) env('ADMIN_EMAIL', 'admin@district23fys.org'))],
            [
                'name' => 'Election Administrator',
                'password' => Hash::make((string) env('ADMIN_PASSWORD', 'Admin@12345!')),
                'is_active' => true,
            ],
        );

        foreach ([
            'President',
            'Vice President',
            'Secretary',
            'Assist Sec',
            'Treasurer',
            'Auditor',
        ] as $index => $name) {
            ElectionPosition::firstOrCreate(
                ['name' => $name],
                [
                    'sort_order' => $index + 1,
                    'seats' => 1,
                    'rule' => 'single',
                    'allow_abstain' => true,
                    'max_selections' => 1,
                    'is_completed' => false,
                    'is_unlocked' => false,
                    'is_closed' => false,
                    'candidacy_open' => false,
                    'nomination_open' => false,
                ],
            );
        }
    }
}
