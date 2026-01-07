<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Jornada;
use App\Models\Ruta;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visita>
 */
class VisitaFactory extends Factory
{
    protected $model = Visita::class;

    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('-2 days', 'now');

        return [
            'usuario_id' => User::factory(),
            'cliente_id' => Cliente::factory(),
            'jornada_id' => Jornada::factory(),
            'ruta_id' => Ruta::factory(), // puedes setear null con state()
            'check_in' => $checkIn,
            'check_out' => fake()->dateTimeBetween($checkIn, '+3 hours'),
            'latitud' => fake()->latitude(-17.6, -17.2),   // rango aproximado CBBA
            'longitud' => fake()->longitude(-66.4, -65.9), // rango aproximado CBBA
            'notas' => fake()->optional()->sentence(),
        ];
    }

    public function sinRuta(): static
    {
        return $this->state(fn () => ['ruta_id' => null]);
    }

    public function soloCheckIn(): static
    {
        return $this->state(function () {
            return ['check_out' => null];
        });
    }
}
