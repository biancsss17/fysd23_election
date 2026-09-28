<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('election_positions', function (Blueprint $table): void {
            $table->boolean('is_closed')->default(false)->after('is_unlocked');
        });
    }

    public function down(): void
    {
        Schema::table('election_positions', function (Blueprint $table): void {
            $table->dropColumn('is_closed');
        });
    }
};
