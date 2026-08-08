<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WasteScan;
use App\Services\PointsService;
use App\Services\DummyAiClassifierService;
use App\Services\PhotoComparisonService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class WasteScanController extends Controller
{
    public function create()
    {
        return view('scan.create');
    }

    public function store(
        Request $request,
        DummyAiClassifierService $classifier,
        PointsService $points,
        PhotoComparisonService $photos
    ) {
        $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
        ]);

        $allowedCategories = config('ecolusyon.allowed_waste_categories', [
            'biodegradable',
            'recyclable',
            'residual',
            'special_hazardous',
            'e_waste',
        ]);

        $photo = $request->file('photo');
        $sha256 = $photos->sha256($photo);

        if ($photos->findExactDuplicate($sha256)) {
            return back()
                ->withErrors([
                    'photo' => 'This photo has already been used for a disposal request.',
                ])
                ->withInput();
        }

        return DB::transaction(function () use (
            $request,
            $photo,
            $sha256,
            $classifier,
            $points,
            $allowedCategories
        ) {
            $path = $photo->store('waste-scans', 'public');
            $result = $classifier->classify($path);

            if (! in_array($result['category'], $allowedCategories, true)) {
                Storage::disk('public')->delete($path);

                return back()
                    ->withErrors([
                        'photo' => 'Only waste or trash photos are allowed.',
                    ])
                    ->withInput();
            }

            $scan = WasteScan::create([
                'user_id' => $request->user()->id,
                'photo_path' => $path,
                'photo_hash' => $sha256,
                'ai_classification' => $result['category'],
                'ai_confidence_score' => $result['confidence'],
                'item_description' => $result['description'],
            ]);

            $ledger = $points->award($request->user(), 'waste_scan', 5, $scan->id);
            $scan->update(['points_awarded' => $ledger->points_earned]);

            return redirect()->route('scan.show', $scan);
        });
    }



    public function show(WasteScan $wasteScan)
    {
        $this->authorizeOwner($wasteScan);

        $threshold = config('ecolusyon.ai_confidence_threshold');
        $isLowConfidence = $wasteScan->ai_confidence_score < $threshold;

        return view('scan.result', compact('wasteScan', 'isLowConfidence'));
    }

    public function confirmCategory(Request $request, WasteScan $wasteScan)
    {
        $this->authorizeOwner($wasteScan);

        $request->validate([
            'category' => ['required', 'in:biodegradable,recyclable,residual,special_hazardous,e_waste'],
        ]);

        $wasteScan->update([
            'ai_classification' => $request->category,
            'ai_confidence_score' => 100,
        ]);

        return redirect()->route('scan.guide', $wasteScan);
    }

    public function guide(Request $request, WasteScan $wasteScan)
    {
        $this->authorizeOwner($wasteScan);

        $barangay = $wasteScan->user->barangay;

        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $allNearbyJunkshops = collect();
        $topNearbyJunkshops = collect();

        if (is_numeric($lat) && is_numeric($lng)) {
            $radiusKm = 10;

            $allNearbyJunkshops = \App\Models\Junkshop::query()
                ->select([
                    'id',
                    'name',
                    'address',
                    'latitude',
                    'longitude',
                    'is_accredited_tsd',
                ])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->map(function ($shop) use ($lat, $lng) {
                    $shop->distance_km = $this->distanceKm(
                        (float) $lat,
                        (float) $lng,
                        (float) $shop->latitude,
                        (float) $shop->longitude
                    );

                    return $shop;
                })
                ->filter(function ($shop) use ($radiusKm) {
                    return $shop->distance_km <= $radiusKm;
                })
                ->sortBy('distance_km')
                ->values();

            $topNearbyJunkshops = $allNearbyJunkshops->take(3)->values();
        }

        return view('scan.guide', [
            'wasteScan' => $wasteScan,
            'barangay' => $barangay,
            'nearbyJunkshops' => $topNearbyJunkshops,   // list
            'mapJunkshops' => $allNearbyJunkshops,      // map
            'userLat' => $lat,
            'userLng' => $lng,
        ]);
    }

    protected function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }


    public function confirmDisposal(
        Request $request,
        WasteScan $wasteScan,
        PhotoComparisonService $photos
    ) {
        $this->authorizeOwner($wasteScan);

        $validated = $request->validate([
            'junkshop_id' => ['required', 'exists:junkshops,id'],
        ]);

        $classification = strtolower(trim($wasteScan->ai_classification));

        if (! in_array($classification, ['recyclable', 'e_waste'], true)) {
            return back()->withErrors([
                'junkshop_id' => 'This waste type must be disposed through the proper channel.',
            ]);
        }

        if (in_array($wasteScan->disposal_status, ['requested', 'approved', 'completed'], true)) {
            return back()->withErrors([
                'junkshop_id' => 'A disposal request has already been submitted for this scan.',
            ]);
        }

        DB::transaction(function () use ($wasteScan, $validated, $photos) {
            $photos->register(
                $wasteScan->user_id,
                $wasteScan->photo_hash,
                'waste_scan',
                $wasteScan->id
            );

            $wasteScan->update([
                'junkshop_id' => $validated['junkshop_id'],
                'disposal_status' => 'requested',
                'disposal_requested_at' => now(),
                'disposal_confirmed' => false,
            ]);
        });

        return redirect()
            ->route('scan.create')
            ->with(
                'status',
                'Disposal request sent successfully. Please wait for the junkshop to approve your request.'
            );
    }

    protected function authorizeOwner(WasteScan $wasteScan): void
    {
        abort_unless((int) $wasteScan->user_id === (int) auth()->id(), 403);
    }

    public function retake(
        Request $request,
        WasteScan $wasteScan,
        DummyAiClassifierService $classifier,
        PhotoComparisonService $photos
    ) {
        $this->authorizeOwner($wasteScan);

        if ($request->isMethod('get')) {
            return view('scan.retake', compact('wasteScan'));
        }

        $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
        ]);

        $allowedCategories = config('ecolusyon.allowed_waste_categories', [
            'biodegradable',
            'recyclable',
            'residual',
            'special_hazardous',
            'e_waste',
        ]);

        $photo = $request->file('photo');
        $sha256 = $photos->sha256($photo);

        if ($photos->findExactDuplicate($sha256)) {
            return back()
                ->withErrors([
                    'photo' => 'This photo has already been used for a disposal request.',
                ])
                ->withInput();
        }

        $oldPath = $wasteScan->photo_path;

        $path = $photo->store('waste-scans', 'public');
        $result = $classifier->classify($path);

        if (! in_array($result['category'], $allowedCategories, true)) {
            Storage::disk('public')->delete($path);

            return back()
                ->withErrors([
                    'photo' => 'Only waste or trash photos are allowed.',
                ])
                ->withInput();
        }

        $wasteScan->update([
            'photo_path' => $path,
            'photo_hash' => $sha256,
            'ai_classification' => $result['category'],
            'ai_confidence_score' => $result['confidence'],
            'item_description' => $result['description'],
            'junkshop_id' => null,
            'disposal_status' => 'not_requested',
            'disposal_requested_at' => null,
            'disposal_approved_at' => null,
            'disposal_completed_at' => null,
            'disposal_confirmed' => false,
        ]);

        if ($oldPath && $oldPath !== $path) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()
            ->route('scan.show', $wasteScan)
            ->with('status', 'Reclassified from your new photo.');
    }



}
