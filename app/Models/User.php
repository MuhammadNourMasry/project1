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

}
