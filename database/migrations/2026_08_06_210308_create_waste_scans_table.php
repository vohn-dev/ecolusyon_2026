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
        Schema::create('waste_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->enum('ai_classification', [
                'biodegradable', 'recyclable', 'residual', 'special_hazardous', 'e_waste',
            ])->nullable();
            $table->unsignedTinyInteger('ai_confidence_score')->nullable(); // 0-100
            $table->string('item_description')->nullable();
            $table->boolean('disposal_confirmed')->default(false);
            $table->unsignedSmallInteger('points_awarded')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_scans');
    }
};
