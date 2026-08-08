<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    protected $fillable = [
        'resident_user_id', 'junkshop_id', 'material_type',
        'estimated_weight_kg', 'is_ewaste', 'status', 'transaction_id',
    ];
    protected $casts = ['is_ewaste' => 'boolean'];

    public function resident()    { return $this->belongsTo(User::class, 'resident_user_id'); }
    public function junkshop()    { return $this->belongsTo(Junkshop::class); }
    public function transaction() { return $this->belongsTo(Transaction::class); }
}

