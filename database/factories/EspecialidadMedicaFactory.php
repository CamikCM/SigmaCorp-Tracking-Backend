<?php

namespace Database\Factories;

use App\Models\EspecialidadMedica;
use Illuminate\Database\Eloquent\Factories\Factory;

class EspecialidadMedicaFactory extends Factory
{
    protected $model = EspecialidadMedica::class;

    public function definition(): array
    {
        return [
            'nombre' => ucfirst($this->faker->unique()->word()),
            'descripcion' => $this->faker->optional()->sentence(),
        ];
    }
}
