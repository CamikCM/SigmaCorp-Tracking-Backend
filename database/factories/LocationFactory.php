<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\VisitadorMedico;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'visitador_medico_id' => VisitadorMedico::factory(),
            'jornada_id' => null,
            'latitude' => $this->faker->latitude(-22, -9),
            'longitude' => $this->faker->longitude(-70, -57),
            'accuracy' => $this->faker->randomFloat(2, 1, 50),
            'speed' => $this->faker->randomFloat(2, 0, 30),
            'heading' => $this->faker->randomFloat(2, 0, 360),
            'altitude' => $this->faker->randomFloat(2, 0, 4500),
            'provider' => $this->faker->randomElement(['gps', 'network']),
            'recorded_at' => now(),
        ];
    }
}
