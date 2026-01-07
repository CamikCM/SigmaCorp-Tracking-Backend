<?php

namespace Database\Factories;

use App\Models\Jornada;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JornadaFactory extends Factory
{
    protected $model = Jornada::class;

    public function definition(): array
    {
        return [
            'usuario_id' => User::factory(),
            'fecha' => fake()->date(),
            'hora_inicio_plan' => '08:00:00',
            'hora_inicio_almuerzo_plan' => '12:00:00',
            'hora_fin_almuerzo_plan' => '14:00:00',
            'hora_fin_plan' => '18:00:00',
            'estado' => 'pendiente',
            'tracking_habilitado' => false,
        ];
    }

    public function activa(): static
    {
        return $this->state(fn () => [
            'estado' => 'activa',
            'tracking_habilitado' => true,
            'inicio_real' => now(),
        ]);
    }
}
