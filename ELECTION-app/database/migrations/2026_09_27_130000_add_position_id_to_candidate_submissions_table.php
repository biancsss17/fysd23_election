<?php

use App\Models\ElectionPosition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_submissions', function (Blueprint $table): void {
            $table->foreignId('position_id')->nullable()->after('id')->constrained('election_positions')->nullOnDelete();
        });

        $firstPosition = ElectionPosition::query()->orderBy('id')->first();
        if ($firstPosition) {
            Schema::getConnection()->table('candidate_submissions')->whereNull('position_id')->update(['position_id' => $firstPosition->id]);
        }
    }

    public function down(): void
    {
        Schema::table('candidate_submissions', function (Blueprint $table): void {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};
