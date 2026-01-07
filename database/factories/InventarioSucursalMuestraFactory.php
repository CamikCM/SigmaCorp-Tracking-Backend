<?php

namespace Database\Factories;

use App\Models\InventarioSucursalMuestra;
use App\Models\Sucursal;
use App\Models\MuestraMedica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventarioSucursalMuestra>
 */
class InventarioSucursalMuestraFactory extends Factory
{
    protected $model = InventarioSucursalMuestra::class;

    public function definition(): array
    {
        return [
            'sucursal_id' => Sucursal::factory(),
            'muestra_medica_id' => MuestraMedica::factory(),
            'cantidad' => fake()->numberBetween(0, 500),
        ];
    }
}
