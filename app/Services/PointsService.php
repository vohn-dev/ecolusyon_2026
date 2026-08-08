<?php

namespace App\Services;

use App\Models\PointsLedger;
use App\Models\User;

class PointsService
{
    public function award(User $user, string $actionType, int $points, ?int $referenceId = null): PointsLedger
    {
        $user->increment('current_points', $points);
        $user->increment('total_points', $points);

        return PointsLedger::create([
            'user_id' => $user->id,
            'action_type' => $actionType,
            'reference_id' => $referenceId,
            'points_earned' => $points,
            'points_redeemed' => 0,
            'balance_after' => $user->fresh()->current_points,
        ]);
    }

    public function redeem(User $user, int $points): PointsLedger
    {
        $user->decrement('current_points', $points);

        return PointsLedger::create([
            'user_id' => $user->id,
            'action_type' => 'redemption',
            'points_earned' => 0,
            'points_redeemed' => $points,
            'balance_after' => $user->fresh()->current_points,
        ]);
    }
}
