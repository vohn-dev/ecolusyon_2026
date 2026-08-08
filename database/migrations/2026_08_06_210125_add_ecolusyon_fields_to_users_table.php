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
        Schema::table('users', function (Blueprint $table) {
             if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['resident', 'junkshop', 'lgu_admin'])
                    ->default('resident')
                    ->after('email');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'barangay_id')) {
                $table->foreignId('barangay_id')
                    ->nullable()
                    ->constrained('barangays')
                    ->nullOnDelete()
                    ->after('phone');
            }

            if (!Schema::hasColumn('users', 'total_points')) {
                $table->unsignedInteger('total_points')->default(0)->after('barangay_id');
            }

            if (!Schema::hasColumn('users', 'current_points')) {
                $table->unsignedInteger('current_points')->default(0)->after('total_points');
            }

                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('barangay_id');
            $table->dropColumn(['role', 'phone', 'total_points', 'current_points']);

        });
    }
};
