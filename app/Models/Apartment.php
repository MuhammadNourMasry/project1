<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
     use HasFactory;
    protected $fillable=['site','type','image','number_of_room','description','price','user_id','city','area','governorate'];
     protected $hidden = ['user_id','id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
     public function Rating(){
        return $this->hasMany(Rating::class);
     }

     public function favoritedByUsers()
{
    return $this->belongsToMany(User::class, 'favorites');
}
}
