<?php

namespace Database\Factories;

use App\Models\InventarioVisitadorMuestra;
use App\Models\MuestraMedica;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventarioVisitadorMuestra>
 */
class InventarioVisitadorMuestraFactory extends Factory
{
    protected $model = InventarioVisitadorMuestra::class;

    public function definition(): array
    {
        return [
            'usuario_id' => User::factory(),
            'muestra_medica_id' => MuestraMedica::factory(),
            'cantidad' => $this->faker->numberBetween(0, 50),
        ];
    }
}
