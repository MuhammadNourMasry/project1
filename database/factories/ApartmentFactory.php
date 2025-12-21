<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        $cities = [
            'Damascus', 'Aleppo', 'Homs', 'Hama', 'Latakia',
            'Tartous', 'Daraa', 'Deir ez-Zor', 'Raqqa',
            'Hasakah', 'Qamishli', 'Idlib', 'Suwayda'
        ];
        $areasByCity = [
            'Damascus' => ['Mezze', 'Kafr Souseh', 'Abu Rummaneh', 'Qasaa', 'Dummar', 'Rukn al-Din', 'Maliki'],
            'Aleppo' => ['New Aleppo', 'Aziziyah', 'Sabil', 'Jamiliyah', 'Shahba', 'Furqan'],
            'Homs' => ['Inshaat', 'Ghouta', 'Waer', 'Hamra'],
            'Hama' => ['Al-Hader', 'Al-Dabagha', 'Al-Arbaeen'],
            'Latakia' => ['Ziraa', 'Mashrou Ziraa', 'Raml Shamali', 'Raml Janoubi', 'Sheikh Daher'],
            'Tartous' => ['Al-Mina', 'Rawda', 'Corniche', 'Kala’a'],
            'Daraa' => ['Mahatta', 'Kashef'],
            'Deir ez-Zor' => ['Joura', 'Qusour'],
            'Raqqa' => ['Mashlab', 'Bustan'],
            'Hasakah' => ['Nashwa', 'Salihiya'],
            'Qamishli' => ['Hilaliya', 'Antariyah'],
            'Idlib' => ['Al-Dowailah', 'Wadi al-Naseem'],
            'Suwayda' => ['Qanawat', 'Maslakh']
        ];
          $images = [
        'images/apartments/1.jpg',
        'images/apartments/2.jpg',
        'images/apartments/3.jpg',
        'images/apartments/4.jpg',
        'images/apartments/5.jpg',
        'images/apartments/6.jpg',
        'images/apartments/7.jpg',
        'images/apartments/8.jpg',
        'images/apartments/9.jpg',
        'images/apartments/10.jpg',
        'images/apartments/11.jpg',
        'images/apartments/12.jpg',
        'images/apartments/13.jpg',
        'images/apartments/14.jpg',
        'images/apartments/15.jpg',
        'images/apartments/16.jpg',
        'images/apartments/17.jpg',
    ];
        $city =fake()->randomElement($cities);
        $site =fake()->randomElement($areasByCity[$city]);
        return [
        'user_id'=> User::factory(),
        'site' => $site,
        'city' => $city,
        'area'=>fake()->numberBetween(40,200),
        'type' =>fake()->randomElement(['home','villa','warehouse']),
        'description'=>fake()->sentence,
        'number_of_room'=>fake()->numberBetween(1,10),
        'rating'=>fake()->randomElement([1,2,3,4,5]),
        'image' => fake()->randomElement($images),
        'price'=>fake()->numberBetween(1500,1000000),
        ];
    }
}
