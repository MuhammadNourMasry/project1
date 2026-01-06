<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserFactory extends Factory
{
    protected static ?string $password;
    public function definition(): array
    {
         $images = [
        'images/users/1.jpg',
        'images/users/2.jpg',
        'images/users/3.jpg',
        'images/users/5.jpg',
        'images/users/5.jpg',
        'images/users/6.jpg',
    ];
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone'=>fake()->unique()->numerify('##########'),
            'date_of_birth'=>fake()->date('Y-m-d'),
            'photo_of_personal_ID'=>'default_id.jpg',
            'personal_photo'=> fake()->randomElement($images),
            'role' => fake()->randomElement(['tenant', 'rented']),
            'is_approved' => fake()->randomElement([true, false]),
            'email' =>fake()->unique()->safeEmail(),
            'created_at' => fake()->dateTimeBetween('-90 days', 'now'),
            'updated_at' => fake()->dateTimeBetween('created_at'),
            'email_verified_at' => now(),
            'password' => 'aAB@1234',
            'remember_token' => Str::random(10),
        ];
    }
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
