<?php

namespace Database\Factories;

use App\Models\EstadoUser;
use App\Models\Jornada;
use App\Models\VisitadorEstadoTracking;
use App\Models\VisitadorMedico;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitadorEstadoTrackingFactory extends Factory
{
    protected $model = VisitadorEstadoTracking::class;

    public function definition(): array
    {
        return [
            'visitador_medico_id' => VisitadorMedico::factory(),
            'jornada_id' => Jornada::factory(),
            'estado_user_id' => EstadoUser::factory(),
            'tipo_marcado' => $this->faker->randomElement(['MANUAL', 'AUTO']),
            'fuente' => $this->faker->randomElement(['MOBILE', 'SYSTEM']),
            'marcado_en' => now(),
            'nota' => $this->faker->optional()->sentence(),
        ];
    }
}
