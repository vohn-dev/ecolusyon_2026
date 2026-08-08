<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waste_scans', function (Blueprint $table) {
            $table->string('photo_hash', 64)->nullable()->index();
            $table->string('photo_phash', 16)->nullable()->index();
        });

        Schema::table('flood_reports', function (Blueprint $table) {
            $table->string('photo_hash', 64)->nullable()->index();
            $table->string('photo_phash', 16)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('waste_scans', function (Blueprint $table) {
            $table->dropColumn(['photo_hash', 'photo_phash']);
        });

        Schema::table('flood_reports', function (Blueprint $table) {
            $table->dropColumn(['photo_hash', 'photo_phash']);
        });
    }
};
