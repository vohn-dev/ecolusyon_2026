<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thing extends Model
{
    protected $fillable = ['user_id','photo_path','status','meta'];
    protected $casts = ['meta' => 'array'];
 
    public function user() { return $this->belongsTo(User::class); }
}

