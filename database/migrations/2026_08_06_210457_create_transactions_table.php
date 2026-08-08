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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('junkshop_id')->constrained()->cascadeOnDelete();
            $table->string('material_type');
            $table->decimal('weight_kg', 8, 2);
            $table->decimal('price_total', 10, 2);
            $table->boolean('is_ewaste')->default(false);
            $table->boolean('routed_to_tsd')->default(false);
            $table->boolean('epr_credit_generated')->default(false);
            $table->unsignedSmallInteger('points_awarded')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
