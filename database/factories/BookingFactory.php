<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
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
            'check_in'=>fake()->date('Y-m-d'),
            'check_out'=>fake()->date('Y-m-d'),
            'status'=>fake()->randomElement(['pending', 'approved', 'rejected', 'cancelled','end']),
        ];
    }
}
