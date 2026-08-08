<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\PickupRequest;
use Illuminate\Http\Request;

class PickupRequestController extends Controller
{
    public function index(Request $request)
    {
        $junkshop = $request->user()->junkshop;
        $status = $request->query('status', 'pending');

        $requests = $junkshop->pickupRequests()
            ->with('resident')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        $counts = [
            'pending' => $junkshop->pickupRequests()->where('status', 'pending')->count(),
            'accepted' => $junkshop->pickupRequests()->where('status', 'accepted')->count(),
            'completed' => $junkshop->pickupRequests()->where('status', 'completed')->count(),
        ];

        return view('operator.requests.index', compact('requests', 'status', 'counts'));
    }

    public function accept(Request $request, PickupRequest $pickupRequest)
    {
        $this->authorizeOwnership($pickupRequest, $request);

        if ($pickupRequest->is_ewaste && ! $pickupRequest->junkshop->is_accredited_tsd) {
            return back()->withErrors([
                'ewaste' => 'This is e-waste and your shop isn\'t marked as an accredited TSD facility. '
                    . 'Update your accreditation in Shop Profile before accepting, or decline so the '
                    . 'resident can find a TSD-accredited shop.',
            ]);
        }

        $pickupRequest->update(['status' => 'accepted']);

        AppNotification::create([
            'user_id' => $pickupRequest->resident_user_id,
            'type' => 'pickup_accepted',
            'message' => "{$pickupRequest->junkshop->name} accepted your {$pickupRequest->material_type} pickup request.",
            'reference_id' => $pickupRequest->id,
        ]);

        return redirect()->route('operator.requests.index')
            ->with('status', 'Request accepted — log the transaction once you\'ve weighed the items.');
    }

    public function decline(Request $request, PickupRequest $pickupRequest)
    {
        $this->authorizeOwnership($pickupRequest, $request);

        $pickupRequest->update(['status' => 'declined']);

        AppNotification::create([
            'user_id' => $pickupRequest->resident_user_id,
            'type' => 'pickup_declined',
            'message' => "{$pickupRequest->junkshop->name} declined your {$pickupRequest->material_type} pickup request.",
            'reference_id' => $pickupRequest->id,
        ]);

        return redirect()->route('operator.requests.index')
            ->with('status', 'Request declined — resident has been notified.');
    }

    private function authorizeOwnership(PickupRequest $pickupRequest, Request $request): void
    {
        abort_unless($pickupRequest->junkshop_id === $request->user()->junkshop->id, 403);
    }
}
