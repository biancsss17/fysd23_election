<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('election_positions', function (Blueprint $table): void {
            $table->unsignedInteger('sort_order')->default(0)->after('id');
        });

        DB::table('election_positions')->orderBy('id')->get()->each(function ($position, int $index): void {
            DB::table('election_positions')->where('id', $position->id)->update(['sort_order' => $index + 1]);
        });
    }

    public function down(): void
    {
        Schema::table('election_positions', function (Blueprint $table): void {
            $table->dropColumn('sort_order');
        });
    }
};
