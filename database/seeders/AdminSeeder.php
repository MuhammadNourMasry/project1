<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::factory()->create([
        'first_name' => 'mouhammed oubai',
        'last_name' => 'jaber',
        'phone' => '9875643210',
        'date_of_birth' => '2004-9-13',
        'photo_of_personal_ID' => 'admin_photo.png',
        'personal_photo' => 'admin_photo.png',
        'role' => 'admin',
        'email' => 'admin@gmail.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
         ]);
    }
}
