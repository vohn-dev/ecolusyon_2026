<?php

namespace App\Http\Controllers;

use App\Models\PointsLedger;
use App\Models\Redemption;
use App\Models\User;
use App\Services\PointsService;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $catalog = config('rewards');

        $ledger = PointsLedger::where('user_id', $user->id)->latest()->take(20)->get();
        $redemptions = Redemption::where('user_id', $user->id)->latest()->get();

        return view('rewards.index', compact('user', 'catalog', 'ledger', 'redemptions'));
    }

    public function redeem(Request $request, string $key, PointsService $points)
    {
        $catalog = config('rewards');
        abort_unless(isset($catalog[$key]), 404);

        $reward = $catalog[$key];
        $user = $request->user();

        if ($user->current_points < $reward['points']) {
            return back()->with('status', 'Not enough points for that reward yet.');
        }

        $points->redeem($user, $reward['points']);

        Redemption::create([
            'user_id' => $user->id,
            'points_spent' => $reward['points'],
            'redemption_type' => $key,
            'status' => 'pending',
        ]);

        return back()->with('status', "Redeemed: {$reward['label']}!");
    }

    public function leaderboard()
    {
        $top = User::where('role', 'resident')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get(['name', 'total_points']);

        return view('rewards.leaderboard', compact('top'));
    }
}
