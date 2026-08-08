<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'household_user_id', 'junkshop_id', 'material_type', 'weight_kg',
        'price_total', 'is_ewaste', 'routed_to_tsd', 'epr_credit_generated', 'points_awarded',
    ];
    protected $casts = ['is_ewaste' => 'boolean', 'routed_to_tsd' => 'boolean', 'epr_credit_generated' => 'boolean'];

    public function resident() { return $this->belongsTo(User::class, 'household_user_id'); }
    public function junkshop() { return $this->belongsTo(Junkshop::class); }

}
