<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
     protected $fillable = ['user_id', 'apartment_id', 'booking_id', 'rating', 'comment'];

     public function User(){
        return $this->belongsTo(User::class);
     }
       public function Apartment(){
        return $this->belongsTo(Apartment::class);
     }
       public function Booking(){
        return $this->belongsTo(Booking::class);
     }
     
}
