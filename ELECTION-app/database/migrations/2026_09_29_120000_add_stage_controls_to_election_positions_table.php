<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('election_positions', function (Blueprint $table): void {
            $table->boolean('candidacy_open')->default(false)->after('is_closed');
            $table->boolean('nomination_open')->default(false)->after('candidacy_open');
        });
    }

    public function down(): void
    {
        Schema::table('election_positions', function (Blueprint $table): void {
            $table->dropColumn(['candidacy_open', 'nomination_open']);
        });
    }
};
