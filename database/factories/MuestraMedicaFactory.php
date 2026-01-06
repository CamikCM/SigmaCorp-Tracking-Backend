<?php

namespace Database\Factories;

use App\Models\MuestraMedica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MuestraMedica>
 */
class MuestraMedicaFactory extends Factory
{
    protected $model = MuestraMedica::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->words(3, true),
            'tipo' => fake()->randomElement(['Tableta', 'Jarabe', 'Inyectable', 'Cápsula', null]),
            'descripcion' => fake()->optional()->sentence(12),
            'activa' => fake()->boolean(90),
        ];
    }

    public function inactiva(): static
    {
        return $this->state(fn () => ['activa' => false]);
    }
}
