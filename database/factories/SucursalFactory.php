<?php

namespace Database\Factories;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

class SucursalFactory extends Factory
{
    protected $model = Sucursal::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company().' - '.$this->faker->citySuffix(),
            'ciudad' => $this->faker->city(),
            'direccion' => $this->faker->address(),
        ];
    }
}
