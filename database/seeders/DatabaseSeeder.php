<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    public function run(): void
    {
     $this->call([AdminSeeder::class,
     UserSeeder::class,
     ApartmentSeeder::class,
     BookingSeeder::class,
     RatingSeeder::class
    ]);
    }
}
