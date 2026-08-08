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

    public function getOpenStatusAttribute(): array
    {
    try {
        [$startRaw, $endRaw] = preg_split('/\s*[–-]\s*/u', $this->operating_hours, 2);

            $now = now();
            $open = \Carbon\Carbon::parse($startRaw)->setDate($now->year, $now->month, $now->day);
            $close = \Carbon\Carbon::parse($endRaw)->setDate($now->year, $now->month, $now->day);

            if ($now->lt($open)) {
                return [
                    'state' => 'closed',
                    'label' => 'Opens in ' . $now->diffAsCarbonInterval($open)->forHumans([
                        'parts' => 1,
                        'short' => true,
                    ]),
                ];
            }

            if ($now->gt($close)) {
                return [
                    'state' => 'closed',
                    'label' => 'Closed — opens tomorrow',
                ];
            }

            return [
                'state' => 'open',
                'label' => 'Closes in ' . $now->diffAsCarbonInterval($close)->forHumans([
                    'parts' => 1,
                    'short' => true,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'state' => 'unknown',
                'label' => $this->operating_hours,
            ];
        }

    }


}
