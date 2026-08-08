<?php

namespace App\Http\Controllers;

use App\Models\WasteScan;
use App\Services\PointsService;
use App\Services\DummyAiClassifierService;
use Illuminate\Http\Request;

class WasteScanController extends Controller
{
    public function create()
    {
        return view('scan.create');
    }

    public function store(Request $request, DummyAiClassifierService $classifier, PointsService $points)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:10240'], // 10MB, matches REST spec
        ]);

        $path = $request->file('photo')->store('waste-scans', 'public');
        $result = $classifier->classify($path);

        $wasteScan = WasteScan::create([
            'user_id' => $request->user()->id,
            'photo_path' => $path,
            'ai_classification' => $result['category'],
            'ai_confidence_score' => $result['confidence'],
            'item_description' => $result['description'],
        ]);

        // +5 pts is awarded on every scan regardless of confidence (per the proposal's points table)
        $ledger = $points->award($request->user(), 'waste_scan', 5, $wasteScan->id);
        $wasteScan->update(['points_awarded' => $ledger->points_earned]);

        return redirect()->route('scan.show', $wasteScan);
    }

    public function show(WasteScan $wasteScan)
    {
        $this->authorizeOwner($wasteScan);

        $threshold = config('ecolusyon.ai_confidence_threshold');
        $isLowConfidence = $wasteScan->ai_confidence_score < $threshold;

        return view('scan.result', compact('scan', 'isLowConfidence'));
    }

    // 5a: low-confidence human-in-the-loop correction (UC-01 alt flow)
    public function confirmCategory(Request $request, WasteScan $wasteScan)
    {
        $this->authorizeOwner($wasteScan);

        $request->validate([
            'category' => ['required', 'in:biodegradable,recyclable,residual,special_hazardous,e_waste'],
        ]);

        $wasteScan->update([
            'ai_classification' => $request->category,
            'ai_confidence_score' => 100, // user-confirmed
        ]);

        return redirect()->route('scan.guide', $wasteScan);
    }

    public function guide(WasteScan $wasteScan)
    {
        $this->authorizeOwner($wasteScan);

        $barangay = $wasteScan->user->barangay;
        $nearbyJunkshops = in_array($wasteScan->ai_classification, ['recyclable', 'e_waste'])
            ? \App\Models\Junkshop::whereJsonContains('materials_accepted', 'PET')
                ->orWhere('is_accredited_tsd', true)
                ->limit(3)->get()
            : collect();

        return view('scan.guide', compact('scan', 'barangay', 'nearbyJunkshops'));
    }

    public function confirmDisposal(Request $request, WasteScan $wasteScan, PointsService $points)
    {
        $this->authorizeOwner($wasteScan);

        if (! $wasteScan->disposal_confirmed) {
            $wasteScan->update(['disposal_confirmed' => true]);
            $points->award($request->user(), 'disposal_confirmed', 10, $wasteScan->id);
        }

        return redirect()->route('dashboard')->with('status', 'Disposal confirmed — +10 pts!');
    }

    protected function authorizeOwner(WasteScan $wasteScan): void
    {
        abort_unless($wasteScan->user_id === auth()->id(), 403);
    }
}
