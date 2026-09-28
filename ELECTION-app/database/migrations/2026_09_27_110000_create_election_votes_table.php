<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_submission_id')->nullable()->constrained('candidate_submissions')->nullOnDelete();
            $table->string('voter_email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_votes');
    }
};
