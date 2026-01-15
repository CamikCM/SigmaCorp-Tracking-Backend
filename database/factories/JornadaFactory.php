<?php

namespace Database\Factories;

use App\Models\Jornada;
use App\Models\VisitadorMedico;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class JornadaFactory extends Factory
{
    protected $model = Jornada::class;

    public function definition(): array
    {
        $fecha = Carbon::now()->subDays($this->faker->numberBetween(0, 3))->startOfDay();
        $inicio = (clone $fecha)->addHours(8);
        $inicioAlm = (clone $fecha)->addHours(12);
        $finAlm = (clone $fecha)->addHours(13);
        $fin = (clone $fecha)->addHours(17);

        return [
            'visitador_medico_id' => VisitadorMedico::factory(),
            'fecha' => $fecha->toDateString(),
            'inicio_jornada' => $inicio,
            'fin_jornada' => $this->faker->boolean(70) ? $fin : null,
            'inicio_almuerzo' => $this->faker->boolean(80) ? $inicioAlm : null,
            'fin_almuerzo' => $this->faker->boolean(80) ? $finAlm : null,
            'estado' => $this->faker->randomElement(['abierta', 'cerrada']),
        ];
    }
}
