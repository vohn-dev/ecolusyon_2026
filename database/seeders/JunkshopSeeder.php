<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Junkshop;

class JunkshopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = [
            ['name' => 'Aling Nena Junkshop', 'operator_name' => 'Nena Reyes', 'address' => 'Rizal St., Brgy. 176, Caloocan',
             'lat' => 14.7580, 'lng' => 120.9830, 'hours' => '7:00 AM – 5:00 PM',
             'materials' => ['PET', 'HDPE', 'cardboard', 'scrap_metal', 'aluminum'], 'tsd' => false],
            ['name' => 'Green Cycle Recyclers', 'operator_name' => 'Boy Santos', 'address' => 'Quirino Hwy, Brgy. Bagong Silangan, QC',
             'lat' => 14.7020, 'lng' => 121.1150, 'hours' => '8:00 AM – 6:00 PM',
             'materials' => ['PET', 'HDPE', 'copper', 'e_waste'], 'tsd' => true],
            ['name' => 'Tanza Bote-Dyaryo', 'operator_name' => 'Lito Cruz', 'address' => 'C-4 Rd., Brgy. Tanza, Navotas',
             'lat' => 14.6570, 'lng' => 120.9420, 'hours' => '6:00 AM – 4:00 PM',
             'materials' => ['cardboard', 'scrap_metal', 'aluminum'], 'tsd' => false],
            ['name' => 'EcoScrap San Roque', 'operator_name' => 'Marites Uy', 'address' => 'M. Naval St., Brgy. San Roque, Navotas',
             'lat' => 14.6630, 'lng' => 120.9390, 'hours' => '7:30 AM – 5:30 PM',
             'materials' => ['PET', 'copper', 'aluminum', 'e_waste'], 'tsd' => true],
        ];

        $benchmarkPrices = [
            'PET' => 12.00, 'HDPE' => 14.00, 'cardboard' => 4.50,
            'scrap_metal' => 18.00, 'aluminum' => 55.00, 'copper' => 320.00, 'e_waste' => 25.00,
        ];

        foreach ($shops as $s) {
            $junkshop = Junkshop::create([
                'name' => $s['name'],
                'operator_name' => $s['operator_name'],
                'address' => $s['address'],
                'latitude' => $s['lat'],
                'longitude' => $s['lng'],
                'operating_hours' => $s['hours'],
                'materials_accepted' => $s['materials'],
                'is_accredited_tsd' => $s['tsd'],
            ]);

            foreach ($s['materials'] as $material) {
                // small random variance around the benchmark, like real junkshops
                $variance = rand(-15, 15) / 100;
                $junkshop->materialPrices()->create([
                    'material_type' => $material,
                    'price_per_kg' => round($benchmarkPrices[$material] * (1 + $variance), 2),
                ]);
            }
        }
    
    }
}
