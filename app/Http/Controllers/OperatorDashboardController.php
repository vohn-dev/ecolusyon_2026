<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class OperatorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $junkshop = $request->user()->junkshop;

        $pendingCount = $junkshop->pickupRequests()->where('status', 'pending')->count();
        $boughtTodayKg = $junkshop->transactions()->whereDate('created_at', today())->sum('weight_kg');
        $earnedTodayPhp = $junkshop->transactions()->whereDate('created_at', today())->sum('price_total');

        $todaysActivity = $junkshop->transactions()
            ->with('resident')
            ->whereDate('created_at', today())
            ->latest()
            ->take(5)
            ->get();

        $unreadCount = AppNotification::where('user_id', $request->user()->id)->whereNull('read_at')->count();

        return view('operator.dashboard', compact(
            'junkshop', 'pendingCount', 'boughtTodayKg', 'earnedTodayPhp', 'todaysActivity', 'unreadCount'
        ));
    }
}
