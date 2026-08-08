<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('points_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action_type'); // e.g. "waste_scan", "disposal_confirmed", "flood_report_verified"
            $table->unsignedBigInteger('reference_id')->nullable(); // id of the waste_scan/flood_report/etc.
            $table->unsignedSmallInteger('points_earned')->default(0);
            $table->unsignedSmallInteger('points_redeemed')->default(0);
            $table->unsignedInteger('balance_after');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_ledger');
    }
};
