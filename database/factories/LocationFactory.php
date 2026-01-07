<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use App\Models\Jornada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'latitude' => $this->faker->latitude(-90, 90),
            'longitude' => $this->faker->longitude(-180, 180),

            // Campos nuevos (si ya aplicaste migración)
            'precision' => $this->faker->randomFloat(2, 1, 50),
            'velocidad' => $this->faker->randomFloat(2, 0, 120),
            'registrado_en' => now(),

            // Relación opcional
            'jornada_id' => null,
        ];
    }

    public function conJornada(?Jornada $jornada = null): static
    {
        return $this->state(function () use ($jornada) {
            $jornada ??= Jornada::factory()->create();
            return [
                'jornada_id' => $jornada->id,
                'user_id' => $jornada->usuario_id, // consistente
            ];
        });
    }
}
