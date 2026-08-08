<?php

namespace App\Http\Controllers;

use App\Models\FloodReport;
use App\Services\PointsService;
use Illuminate\Http\Request;
use App\Services\PhotoComparisonService;
use Illuminate\Support\Facades\DB;


class FloodReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = FloodReport::where('user_id', $request->user()->id)->latest()->get();
        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request, PhotoComparisonService $photos)
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'severity' => ['required', 'in:minor,partial_blockage,full_blockage'],
            'waste_types' => ['required', 'array', 'min:1'],
            'waste_types.*' => ['in:plastic_bags,sachets,construction_debris,organic_matter'],
        ]);

        $photo = $request->file('photo');
        $sha256 = $photos->sha256($photo);

        if ($photos->findExactDuplicate($sha256)) {
            return back()
                ->withErrors([
                    'photo' => 'This photo has already been submitted.',
                ])
                ->withInput();
        }

        return DB::transaction(function () use ($request, $validated, $photo, $sha256, $photos) {
            $path = $photo->store('flood-reports', 'public');

            $report = FloodReport::create([
                'user_id' => $request->user()->id,
                'photo_path' => $path,
                'photo_hash' => $sha256,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'severity' => $validated['severity'],
                'waste_types_observed' => $validated['waste_types'],
                'status' => 'submitted',
            ]);

            $photos->register(
                $request->user()->id,
                $sha256,
                'flood_report',
                $report->id
            );

            return redirect()->route('reports.index')
                ->with('status', 'Report submitted — it will appear on the heatmap once verified.');
        });
    }

    public function verifyCleanup(Request $request, FloodReport $report, PointsService $points)
    {
        abort_unless($report->user_id === $request->user()->id, 403);
        abort_unless($report->status === 'cleaned', 400, 'This report is not in a cleaned state yet.');

        $report->update(['status' => 'confirmed']);
        $points->award($request->user(), 'cleanup_follow_up_verified', 5, $report->id);

        return back()->with('status', 'Thanks for confirming — +5 pts!');
    }

    public function heatmap()
    {
        $reports = FloodReport::whereIn('status', ['verified', 'dispatched', 'cleaned', 'confirmed'])
            ->select('id', 'latitude', 'longitude', 'severity', 'status')
            ->get();

        return view('heatmap', compact('reports'));
    }

    public function autoDetect(Request $request, \App\Services\DummyAiClassifierService $classifier)
    {
        $request->validate(['photo' => ['required', 'image', 'max:10240']]);

        $path = $request->file('photo')->store('flood-reports/auto-detect', 'public');
        $result = $classifier->classifyFloodWaste($path);

        return response()->json($result);
    }

}
