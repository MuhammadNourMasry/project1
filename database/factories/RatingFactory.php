<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=> User::factory(),
            'apartment_id'=>Apartment::factory(),
            'booking_id'=>Booking::factory(),
             'rating'=>fake()->randomElement(['1','2','3','4','5']),
             'comment'=>fake()->sentence
        ];
    }
}
