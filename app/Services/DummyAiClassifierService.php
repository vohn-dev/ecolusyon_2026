<?php

namespace App\Services;

class DummyAiClassifierService
{
    protected array $categories = [
        'biodegradable', 'recyclable', 'residual', 'special_hazardous', 'e_waste',
    ];

    /**
     * Returns a fake-but-realistic classification so the UI + confidence-branch
     * logic can be built and demoed without calling a real vision API.
     *
     * When you're ready for the real thing, write a separate class (e.g.
     * OpenAiVisionClassifierService) with this same classify() signature —
     *   Http::withToken(config('services.openai.key'))
     *       ->attach('image', file_get_contents($photoPath), 'scan.jpg')
     *       ->post('https://api.openai.com/v1/...', [...]);
     * — and bind that one in place of this dummy. Keep the same return shape
     * so the controller and views don't have to change at all.
     */
    public function classify(string $photoPath): array
    {
        $category = $this->categories[array_rand($this->categories)];
        $confidence = rand(45, 98); // deliberately spans below/above the 70% threshold

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
