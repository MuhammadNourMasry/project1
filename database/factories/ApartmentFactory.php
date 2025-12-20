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
        'price'=>fake()->numberBetween(1500,1000000),
        ];
    }
}
