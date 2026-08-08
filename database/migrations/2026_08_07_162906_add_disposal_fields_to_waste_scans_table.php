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
        Schema::table('waste_scans', function (Blueprint $table) {
            $table->foreignId('junkshop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('disposal_status')->default('not_requested'); // not_requested, requested, approved, completed, rejected
            $table->timestamp('disposal_requested_at')->nullable();
            $table->timestamp('disposal_approved_at')->nullable();
            $table->timestamp('disposal_completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waste_scans', function (Blueprint $table) {
            //
        });
    }
};
