<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialPrice extends Model
{
    protected $fillable = ['junkshop_id', 'material_type', 'price_per_kg'];

    public function junkshop() { return $this->belongsTo(Junkshop::class); }

}
