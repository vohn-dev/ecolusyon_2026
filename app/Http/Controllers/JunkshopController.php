<?php

namespace App\Http\Controllers;

use App\Models\Junkshop;
use App\Models\Transaction;
use App\Services\PointsService;
use Illuminate\Http\Request;

class JunkshopController extends Controller
{
    public function index(Request $request)
    {
        $material = $request->query('material');

        $junkshops = Junkshop::with('materialPrices')
            ->when($material, fn ($q) => $q->whereJsonContains('materials_accepted', $material))
            ->paginate(3)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('market._junkshop-items', ['junkshops' => $junkshops])->render(),
                'has_more' => $junkshops->hasMorePages(),
            ]);
        }

        $materialIcons = [
            'PET' => 'bi-cup-straw',
            'HDPE' => 'bi-droplet-half',
            'cardboard' => 'bi-box-seam',
            'scrap_metal' => 'bi-nut',
            'aluminum' => 'bi-recycle',
            'copper' => 'bi-lightning-charge',
            'e_waste' => 'bi-cpu',
        ];

        return view('market.index', compact('junkshops', 'materialIcons', 'material'));
    }


    public function show(Junkshop $junkshop)
    {
        $junkshop->load('materialPrices');
        return view('market.show', compact('junkshop'));
    }

    public function schedule(Request $request, Junkshop $junkshop, PointsService $points)
    {
        $validated = $request->validate([
            'material_type' => ['required', 'string'],
            'weight_kg' => ['required', 'numeric', 'min:0.1'],
            'is_ewaste' => ['nullable', 'boolean'],
        ]);

        $price = $junkshop->materialPrices()
            ->where('material_type', $validated['material_type'])
            ->firstOrFail(); 

        $isEwaste = (bool) ($validated['is_ewaste'] ?? false);
        $priceTotal = round($price->price_per_kg * $validated['weight_kg'], 2);

        $transaction = Transaction::create([
            'household_user_id' => $request->user()->id,
            'junkshop_id' => $junkshop->id,
            'material_type' => $validated['material_type'],
            'weight_kg' => $validated['weight_kg'],
            'price_total' => $priceTotal,
            'is_ewaste' => $isEwaste,
            'routed_to_tsd' => $isEwaste && $junkshop->is_accredited_tsd,
            'epr_credit_generated' => false,
        ]);

        $basePoints = 10 + (int) floor($validated['weight_kg'] * 5);
        $totalPoints = $transaction->routed_to_tsd ? $basePoints * 2 : $basePoints;

        $ledger = $points->award($request->user(), 'junkconnect_transaction', $totalPoints, $transaction->id);
        $transaction->update(['points_awarded' => $ledger->points_earned]);

        return redirect()->route('market.history')
            ->with('status', "Transaction logged — ₱{$priceTotal} · +{$totalPoints} pts!");
    }

    public function history(Request $request)
    {
        $transactions = Transaction::with('junkshop')
            ->where('household_user_id', $request->user()->id)
            ->latest()->get();

        return view('market.history', compact('transactions'));
    }
}
