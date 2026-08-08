<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'notifications';
    protected $fillable = ['user_id', 'type', 'message', 'reference_id', 'read_at'];
    protected $casts = ['read_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}

