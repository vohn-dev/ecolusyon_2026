<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class OperatorNotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)->latest()->get();

        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('operator.notifications.index', compact('notifications'));
    }
}
