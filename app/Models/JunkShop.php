<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Junkshop extends Model
{
    protected $fillable = [
        'name', 'operator_name', 'address', 'latitude', 'longitude',
        'operating_hours', 'materials_accepted', 'is_accredited_tsd',
    ];
    protected $casts = ['materials_accepted' => 'array', 'is_accredited_tsd' => 'boolean'];

    public function materialPrices() { return $this->hasMany(MaterialPrice::class); }
    public function transactions()   { return $this->hasMany(Transaction::class); }
}
