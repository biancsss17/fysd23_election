<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voter_position_ballots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('position_id')->constrained('election_positions')->cascadeOnDelete();
            $table->string('voter_email');
            $table->timestamps();
            $table->unique(['position_id', 'voter_email']);
        });

        DB::table('election_votes')
            ->select('position_id', 'voter_email')
            ->whereNotNull('position_id')
            ->distinct()
            ->orderBy('position_id')
            ->chunk(500, function ($votes): void {
                foreach ($votes as $vote) {
                    DB::table('voter_position_ballots')->insertOrIgnore([
                        'position_id' => $vote->position_id,
                        'voter_email' => $vote->voter_email,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('voter_position_ballots');
    }
};
