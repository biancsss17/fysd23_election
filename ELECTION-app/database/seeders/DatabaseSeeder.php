<?php

namespace Database\Seeders;

use App\Models\AdminUser;
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

    }
}
