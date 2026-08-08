<?php

// database/migrations/xxxx_xx_xx_add_owner_user_id_to_junkshops_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('junkshops', function (Blueprint $table) {
            $table->foreignId('owner_user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('junkshops', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_user_id');
        });
    }
};
