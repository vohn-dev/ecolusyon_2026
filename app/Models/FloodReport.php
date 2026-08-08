<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloodReport extends Model
{
    protected $fillable = [
        'user_id', 'latitude', 'longitude', 'photo_path', 'severity',
        'waste_types_observed', 'status', 'verified_by_lgu',
        'cleanup_completed_at', 'points_awarded',
    ];
    protected $casts = [
        'waste_types_observed' => 'array',
        'verified_by_lgu' => 'boolean',
        'cleanup_completed_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
