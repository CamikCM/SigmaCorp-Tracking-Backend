<?php

namespace Database\Factories;

use App\Models\AsignacionRuta;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AsignacionRuta>
 */
class AsignacionRutaFactory extends Factory
{
    protected $model = AsignacionRuta::class;

    public function definition(): array
    {
        $inicio = fake()->dateTimeBetween('-15 days', '+15 days');
        $fin = (clone $inicio);
        $fin->modify('+'.fake()->numberBetween(0, 30).' days');

        return [
            'ruta_id' => Ruta::factory(),
            'usuario_id' => User::factory(),
            'fecha_inicio' => $inicio->format('Y-m-d'),
            'fecha_fin' => fake()->boolean(70) ? $fin->format('Y-m-d') : null,
            'activa' => true,
        ];
    }

    public function inactiva(): static
    {
        return $this->state(fn () => ['activa' => false]);
    }
}
