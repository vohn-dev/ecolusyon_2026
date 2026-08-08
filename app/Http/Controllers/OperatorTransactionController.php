<?php

namespace App\Http\Controllers;

use App\Models\PickupRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperatorTransactionController extends Controller
{
    public function index(Request $request)
    {
        $junkshop = $request->user()->junkshop;

        $acceptedRequests = $junkshop->pickupRequests()
            ->where('status', 'accepted')->with('resident')->latest()->get();

        $recent = $junkshop->transactions()->with('resident')->latest()->take(10)->get();

        $weekTotal = $junkshop->transactions()->where('created_at', '>=', now()->startOfWeek())->sum('price_total');
        $weekWeight = $junkshop->transactions()->where('created_at', '>=', now()->startOfWeek())->sum('weight_kg');

        return view('operator.transactions.index', [
            'junkshop' => $junkshop,
            'acceptedRequests' => $acceptedRequests,
            'recent' => $recent,
            'weekTotal' => $weekTotal,
            'weekWeight' => $weekWeight,
            'materials' => $junkshop->materialPrices()->pluck('price_per_kg', 'material_type'),
        ]);
    }

    public function store(Request $request, PointsService $points)
    {
        $junkshop = $request->user()->junkshop;

        $validated = $request->validate([
            'pickup_request_id' => ['nullable', 'exists:pickup_requests,id'],
            'resident_email' => ['nullable', 'email'], 
            'material_type' => ['required', 'string'],
            'weight_kg' => ['required', 'numeric', 'min:0.1'],
            'is_ewaste' => ['nullable', 'boolean'],
        ]);

        $price = $junkshop->materialPrices()
            ->where('material_type', $validated['material_type'])
            ->firstOrFail(); 

        $pickupRequest = null;
        $residentId = null;

        if (! empty($validated['pickup_request_id'])) {
            // Main flow step 1: confirm the household arranged via the app.
            $pickupRequest = PickupRequest::where('junkshop_id', $junkshop->id)
                ->where('status', 'accepted')
                ->findOrFail($validated['pickup_request_id']);
            $residentId = $pickupRequest->resident_user_id;
        } elseif (! empty($validated['resident_email'])) {
            $residentId = User::where('email', $validated['resident_email'])->where('role', 'resident')->value('id');
        }

        $isEwaste = (bool) ($validated['is_ewaste'] ?? false);
        $priceTotal = round($price->price_per_kg * $validated['weight_kg'], 2);
        $routedToTsd = $isEwaste && $junkshop->is_accredited_tsd;

        $transaction = DB::transaction(function () use (
            $junkshop, $validated, $priceTotal, $isEwaste, $routedToTsd, $residentId, $pickupRequest, $points, $request
        ) {
            $transaction = Transaction::create([
                'household_user_id' => $residentId,
                'junkshop_id' => $junkshop->id,
                'pickup_request_id' => $pickupRequest?->id,
                'material_type' => $validated['material_type'],
                'weight_kg' => $validated['weight_kg'],
                'price_total' => $priceTotal,
                'is_ewaste' => $isEwaste,
                'routed_to_tsd' => $routedToTsd,
                'epr_credit_generated' => $routedToTsd,
            ]);

            if ($pickupRequest) {
                $pickupRequest->update(['status' => 'completed', 'transaction_id' => $transaction->id]);
            }

            $multiplier = $routedToTsd ? 2 : 1;
            $pointsAwarded = 0;

            if ($residentId) {
                $householdBase = 10 + (int) floor($validated['weight_kg'] * 5);
                $ledger = $points->award(User::find($residentId), 'junkconnect_transaction', $householdBase * $multiplier, $transaction->id);
                $pointsAwarded += $ledger->points_earned;
            }

            $operatorBase = (int) floor($validated['weight_kg'] * 5);
            $operatorLedger = $points->award($request->user(), 'junkconnect_transaction_operator', $operatorBase * $multiplier, $transaction->id);
            $pointsAwarded += $operatorLedger->points_earned;

            $transaction->update(['points_awarded' => $pointsAwarded]);

            return $transaction;
        });

        return redirect()->route('operator.transactions.index')
            ->with('status', "Transaction logged — ₱{$transaction->price_total} for {$transaction->weight_kg}kg · +{$transaction->points_awarded} pts total.");
    }
}
