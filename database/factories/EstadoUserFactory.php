<?php

namespace Database\Factories;

use App\Models\EstadoUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstadoUserFactory extends Factory
{
    protected $model = EstadoUser::class;

    public function definition(): array
    {
        $codigo = $this->faker->randomElement(['OFF', 'ON']);

        return [
            'codigo' => $codigo,
            'nombre' => $codigo === 'ON' ? 'Encendido' : 'Apagado',
        ];
    }

    public function on(): static
    {
        return $this->state(fn () => ['codigo' => 'ON', 'nombre' => 'Encendido']);
    }

    public function off(): static
    {
        return $this->state(fn () => ['codigo' => 'OFF', 'nombre' => 'Apagado']);
    }
}
