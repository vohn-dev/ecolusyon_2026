<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteScan extends Model
{
    protected $fillable = [
        'user_id', 'photo_path', 'ai_classification', 'ai_confidence_score',
        'item_description', 'disposal_confirmed', 'points_awarded',
    ];
    protected $casts = ['disposal_confirmed' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
}

