<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
      protected $fillable = ['user_id','apartment_id','check_in','check_out','status'];
         protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];
      public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
     public function Rating(){
        return $this->hasOne(Rating::class);
     }
    
}
