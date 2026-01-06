<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;
    protected $fillable = [
        'phone',
        'password',
        'first_name',
        'last_name',
        'email',
        'date_of_birth',
        'role',
        'is_approved',
        'photo_of_personal_ID',
        'personal_photo',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'id',
    ];
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    public function apartment()
    {
        return $this->hasMany(Apartment::class);
    }
      public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
      public function Rating(){
        return $this->hasMany(Rating::class);
     }

     public function favoriteApartments()
{
    return $this->belongsToMany(Apartment::class, 'favorites');
}


        public function scopeNonAdmin($query) {
        return $query->where('role', '!=', 'admin');
    }

    public function scopeApproved($query) {
        return $query->where('is_approved', 1);
    }
    public function scopePending($query) {
        return $query->where('is_approved', 0);
    }

    public function scopeCreatedBetween($query, $from, $to) {
        return $query
            ->where('created_at', '>=', $from)
            ->where('created_at', '<',  $to);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApproved(): bool
    {
        return (int) $this->is_approved === 1;
    }

}
