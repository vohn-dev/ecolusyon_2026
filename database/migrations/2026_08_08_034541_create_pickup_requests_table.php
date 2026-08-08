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
        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('junkshop_id')->constrained()->cascadeOnDelete();
            $table->string('material_type');
            $table->decimal('estimated_weight_kg', 8, 2);
            $table->boolean('is_ewaste')->default(false);
            $table->enum('status', ['pending', 'accepted', 'declined', 'completed'])->default('pending');
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_requests');
    }
};
