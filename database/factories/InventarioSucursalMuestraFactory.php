<?php

namespace Database\Factories;

use App\Models\InventarioSucursalMuestra;
use App\Models\MuestraMedica;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventarioSucursalMuestraFactory extends Factory
{
    protected $model = InventarioSucursalMuestra::class;

    public function definition(): array
    {
        return [
            'sucursal_id' => Sucursal::factory(),
            'muestra_medica_id' => MuestraMedica::factory(),
            'cantidad' => $this->faker->numberBetween(0, 500),
        ];
    }
}
