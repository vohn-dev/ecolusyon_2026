<?php

namespace App\Http\Controllers;

use App\Models\Junkshop;
use App\Models\Transaction;
use App\Services\PointsService;
use Illuminate\Http\Request;
use App\Models\PickupRequest;

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

    public function schedule(Request $request, Junkshop $junkshop)
    {
        $validated = $request->validate([
            'material_type' => ['required', 'string'],
            'weight_kg' => ['required', 'numeric', 'min:0.1'],
            'is_ewaste' => ['nullable', 'boolean'],
        ]);

        $junkshop->materialPrices()
            ->where('material_type', $validated['material_type'])
            ->firstOrFail(); // still enforces: material must have an active price row

        PickupRequest::create([
            'resident_user_id' => $request->user()->id,
            'junkshop_id' => $junkshop->id,
            'material_type' => $validated['material_type'],
            'estimated_weight_kg' => $validated['weight_kg'],
            'is_ewaste' => (bool) ($validated['is_ewaste'] ?? false),
            'status' => 'pending',
        ]);

        return redirect()->route('market.index')
            ->with('status', 'Request sent! ' . $junkshop->name . ' will accept or decline it shortly.');
    }


    public function history(Request $request)
    {
        $transactions = Transaction::with('junkshop')
            ->where('household_user_id', $request->user()->id)
            ->latest()->get();

        return view('market.history', compact('transactions'));
    }

    
}
