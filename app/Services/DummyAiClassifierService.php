<?php

namespace App\Services;

class DummyAiClassifierService
{
    protected array $categories = [
        'biodegradable', 'recyclable', 'residual', 'special_hazardous', 'e_waste',
    ];

    public function classify(string $photoPath): array
    {
        $category = $this->categories[array_rand($this->categories)];
        $confidence = rand(45, 98); 

        $descriptions = [
            'biodegradable' => 'Food scraps / kitchen waste',
            'recyclable' => 'PET plastic bottle',
            'residual' => 'Mixed sachet wrapper',
            'special_hazardous' => 'Used battery',
            'e_waste' => 'Broken phone charger',
        ];

        return [
            'category' => $category,
            'confidence' => $confidence,
            'description' => $descriptions[$category],
        ];
    }
}
