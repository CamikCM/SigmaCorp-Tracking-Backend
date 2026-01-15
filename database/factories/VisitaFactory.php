<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Jornada;
use App\Models\Visita;
use App\Models\VisitadorMedico;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitaFactory extends Factory
{
    protected $model = Visita::class;

    public function definition(): array
    {
        return [
            'visitador_medico_id' => VisitadorMedico::factory(),
            'cliente_id' => Cliente::factory(),
            'jornada_id' => null,
            'fecha' => now(),
            'latitud' => $this->faker->latitude(-22, -9),
            'longitud' => $this->faker->longitude(-70, -57),
            'observaciones' => $this->faker->optional()->sentence(),
        ];
    }
}
