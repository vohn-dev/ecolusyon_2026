<?php

namespace App\Http\Controllers;

use App\Models\MaterialPrice;
use Illuminate\Http\Request;

class MaterialPriceController extends Controller
{
    // Metro Manila reference ranges — a stand-in for a real market-data feed.
    private const BENCHMARKS = [
        'PET'         => [7.20, 9.10],
        'HDPE'        => [5.50, 7.00],
        'cardboard'   => [3.50, 4.50],
        'scrap_metal' => [18.00, 24.00],
        'aluminum'    => [55.00, 70.00],
        'copper'      => [280.00, 330.00],
    ];

    public function edit(Request $request)
    {
        $junkshop = $request->user()->junkshop;
        $prices = $junkshop->materialPrices()->get()->keyBy('material_type');

        return view('operator.prices.edit', [
            'junkshop' => $junkshop,
            'prices' => $prices,
            'materials' => ShopProfileController::MATERIALS,
            'benchmarks' => self::BENCHMARKS,
        ]);
    }

    public function update(Request $request)
    {
        $junkshop = $request->user()->junkshop;

        $validated = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($validated['prices'] as $materialType => $pricePerKg) {
            if ($pricePerKg === null || $pricePerKg === '') {
                continue;
            }

            // UC-06 business rule: one active price row per material — update, don't duplicate.
            MaterialPrice::updateOrCreate(
                ['junkshop_id' => $junkshop->id, 'material_type' => $materialType],
                ['price_per_kg' => $pricePerKg]
            );
        }

        return redirect()->route('operator.prices.edit')
            ->with('status', 'Prices updated — now live in resident-facing JunkConnect search.');
    }
}
