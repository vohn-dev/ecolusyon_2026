<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoFingerprint extends Model
{
    protected $fillable = [
        'user_id',
        'photo_hash',
        'source_type',
        'source_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
