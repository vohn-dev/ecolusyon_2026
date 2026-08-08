<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsLedger extends Model
{
    protected $table = 'points_ledger';
    protected $fillable = ['user_id', 'action_type', 'reference_id', 'points_earned', 'points_redeemed', 'balance_after'];

    public function user() { return $this->belongsTo(User::class); }
}




