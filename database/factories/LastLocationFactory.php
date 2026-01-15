<?php

namespace Database\Factories;

use App\Models\LastLocation;
use App\Models\VisitadorMedico;
use Illuminate\Database\Eloquent\Factories\Factory;

class LastLocationFactory extends Factory
{
    protected $model = LastLocation::class;

    public function definition(): array
    {
        return [
            'visitador_medico_id' => VisitadorMedico::factory(),
            'latitude' => $this->faker->latitude(-22, -9),
            'longitude' => $this->faker->longitude(-70, -57),
            'recorded_at' => now(),
        ];
    }
}
