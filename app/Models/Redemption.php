<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redemption extends Model
{
    protected $fillable = ['user_id', 'points_spent', 'redemption_type', 'status'];

    public function user() { return $this->belongsTo(User::class); }
}
