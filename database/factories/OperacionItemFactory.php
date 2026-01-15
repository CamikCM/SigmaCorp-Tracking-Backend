<?php

namespace Database\Factories;

use App\Models\MuestraMedica;
use App\Models\Operacion;
use App\Models\OperacionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OperacionItemFactory extends Factory
{
    protected $model = OperacionItem::class;

    public function definition(): array
    {
        return [
            'operacion_id' => Operacion::factory(),
            'muestra_medica_id' => MuestraMedica::factory(),
            'cantidad' => $this->faker->numberBetween(1, 50),
        ];
    }
}
