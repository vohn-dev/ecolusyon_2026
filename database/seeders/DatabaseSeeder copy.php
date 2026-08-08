<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barangay;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
        BarangaySeeder::class,
        JunkshopSeeder::class,
    ]);

    // one demo resident so you have something to log in as immediately
    User::factory()->create([
        'name' => 'Mira Santos',
        'email' => 'mira@example.com',
        'password' => bcrypt('password'),
        'role' => 'resident',
        'barangay_id' => Barangay::first()->id,
        'phone' => '09171234567',
    ]);

    }
}
