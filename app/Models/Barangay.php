<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $fillable = [
        'name',
        'city',
        'geojson_boundary',
        'collection_schedule',
    ];

    protected $casts = [
        'geojson_boundary' => 'array',
        'collection_schedule' => 'array',
    ];

    public function users() { return $this->hasMany(User::class); }
}
