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
        Schema::create('junkshops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('operator_name')->nullable(); // dummy stand-in — no real Junkshop-role user
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('operating_hours')->nullable();
            $table->json('materials_accepted'); // ["PET","HDPE","cardboard","scrap_metal","e_waste"]
            $table->boolean('is_accredited_tsd')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('junkshops');
    }
};
