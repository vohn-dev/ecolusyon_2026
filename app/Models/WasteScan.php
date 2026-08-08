<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteScan extends Model
{
    protected $fillable = [
        'user_id',
        'photo_path',
        'photo_hash',
        'ai_classification',
        'ai_confidence_score',
        'item_description',
        'junkshop_id',
        'disposal_status',
        'disposal_requested_at',
        'disposal_approved_at',
        'disposal_completed_at',
        'disposal_confirmed',
        'points_awarded',
    ];

    protected $casts = [
        'disposal_confirmed' => 'boolean',
        'disposal_requested_at' => 'datetime',
        'disposal_approved_at' => 'datetime',
        'disposal_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function junkshop()
    {
        return $this->belongsTo(Junkshop::class);
    }
}