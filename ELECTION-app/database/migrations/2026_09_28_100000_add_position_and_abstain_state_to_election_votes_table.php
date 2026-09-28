<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('election_votes', function (Blueprint $table): void {
            $table->foreignId('position_id')->nullable()->after('id')->constrained('election_positions')->nullOnDelete();
            $table->boolean('is_abstain')->default(false)->after('voter_email');
        });

        DB::statement('UPDATE election_votes SET position_id = (SELECT position_id FROM candidate_submissions WHERE candidate_submissions.id = election_votes.candidate_submission_id) WHERE position_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('election_votes', function (Blueprint $table): void {
            $table->dropForeign(['position_id']);
            $table->dropColumn(['position_id', 'is_abstain']);
        });
    }
};
