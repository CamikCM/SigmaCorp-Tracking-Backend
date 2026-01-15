<?php

namespace Database\Factories;

use App\Models\InventarioVisitadorMuestra;
use App\Models\MuestraMedica;
use App\Models\VisitadorMedico;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventarioVisitadorMuestraFactory extends Factory
{
    protected $model = InventarioVisitadorMuestra::class;

    public function definition(): array
    {
        return [
            'visitador_medico_id' => VisitadorMedico::factory(),
            'muestra_medica_id' => MuestraMedica::factory(),
            'cantidad' => $this->faker->numberBetween(0, 200),
        ];
    }
}
