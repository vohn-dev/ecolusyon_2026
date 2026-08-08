<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointsLedger;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $activity = PointsLedger::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'activity' => $activity,
        ]);
    }
}

