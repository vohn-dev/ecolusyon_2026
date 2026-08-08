<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barangay;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = [
            ['name' => 'Barangay 176', 'city' => 'Caloocan City'],
            ['name' => 'Barangay 178', 'city' => 'Caloocan City'],
            ['name' => 'Barangay Bagong Silangan', 'city' => 'Quezon City'],
            ['name' => 'Barangay Tanza', 'city' => 'Navotas City'],
            ['name' => 'Barangay San Roque', 'city' => 'Navotas City'],
        ];

        foreach ($barangays as $b) {
            Barangay::create([
                ...$b,
                'collection_schedule' => [
                    'biodegradable' => 'Mon, Thu',
                    'residual' => 'Tue, Fri',
                    'recyclable' => 'Wed, Sat',
                ],
            ]);
        }

    }
}
