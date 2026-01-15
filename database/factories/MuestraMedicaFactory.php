<?php

namespace Database\Factories;

use App\Models\MuestraMedica;
use Illuminate\Database\Eloquent\Factories\Factory;

class MuestraMedicaFactory extends Factory
{
    protected $model = MuestraMedica::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Muestra '.$this->faker->unique()->word(),
            'descripcion' => $this->faker->optional()->sentence(),
        ];
    }
}
