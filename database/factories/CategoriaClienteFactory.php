<?php

namespace Database\Factories;

use App\Models\CategoriaCliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaClienteFactory extends Factory
{
    protected $model = CategoriaCliente::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->optional()->sentence(),
        ];
    }
}
