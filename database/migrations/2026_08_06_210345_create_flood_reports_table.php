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
        Schema::create('flood_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('photo_path');
            $table->enum('severity', ['minor', 'partial_blockage', 'full_blockage']);
            $table->json('waste_types_observed'); // ["plastic_bags","sachets",...]
            $table->enum('status', [
                'submitted', 'verified', 'dispatched', 'cleaned', 'confirmed', 'rejected',
            ])->default('submitted');
            $table->boolean('verified_by_lgu')->default(false);
            $table->timestamp('cleanup_completed_at')->nullable();
            $table->unsignedSmallInteger('points_awarded')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flood_reports');
    }
};
