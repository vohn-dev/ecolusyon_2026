<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $junkshop = $request->user()->junkshop;

        $summary = [
            'total_income' => $junkshop->transactions()->sum('price_total'),
            'total_volume_kg' => $junkshop->transactions()->sum('weight_kg'),
            'households_served' => $junkshop->transactions()->whereNotNull('household_user_id')->distinct('household_user_id')->count('household_user_id'),
            'tsd_routed_kg' => $junkshop->transactions()->where('routed_to_tsd', true)->sum('weight_kg'),
        ];

        $byMaterial = $junkshop->transactions()
            ->selectRaw('material_type, SUM(weight_kg) as kg, SUM(price_total) as total')
            ->groupBy('material_type')
            ->orderByDesc('kg')
            ->get();

        $weekly = $junkshop->transactions()
            ->selectRaw('YEARWEEK(created_at) as yw, SUM(price_total) as total, SUM(weight_kg) as kg')
            ->groupBy('yw')
            ->orderByDesc('yw')
            ->take(8)
            ->get();

        return view('operator.analytics.index', compact('summary', 'byMaterial', 'weekly'));
    }

    public function export(Request $request)
    {
        $junkshop = $request->user()->junkshop;
        $transactions = $junkshop->transactions()->with('resident')->latest()->get();
        $filename = 'ecolusyon-income-'.$junkshop->id.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($transactions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Material', 'Weight (kg)', 'Price Total', 'E-waste', 'Routed to TSD', 'Resident']);

            foreach ($transactions as $t) {
                fputcsv($out, [
                    $t->created_at->format('Y-m-d H:i'),
                    $t->material_type,
                    $t->weight_kg,
                    $t->price_total,
                    $t->is_ewaste ? 'Yes' : 'No',
                    $t->routed_to_tsd ? 'Yes' : 'No',
                    $t->resident->name ?? 'Walk-in',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
