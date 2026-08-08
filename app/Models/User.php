<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'barangay_id',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function barangay(){ return $this->belongsTo(Barangay::class); }
    public function wasteScans() { return $this->hasMany(WasteScan::class); }
    public function floodReports() { return $this->hasMany(FloodReport::class); }
    public function transaction() { return $this->hasMany(Transaction::class, 'household_user_id'); }
    public function pointsLedger() { return $this->hasMany(PointLedger::class); }
    public function redemptions() { return $this->hasMany(Redemption::class); }

    public function isResident(): bool { return $this->role === 'resident'; }
}
