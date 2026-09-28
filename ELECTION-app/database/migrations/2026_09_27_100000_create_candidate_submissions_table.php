<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('submitted_by_email');
            $table->string('submission_type', 20);
            $table->string('status', 20)->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_submissions');
    }
};
