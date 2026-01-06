<?php

namespace Database\Factories;

use App\Models\CategoriaCliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoriaCliente>
 */
class CategoriaClienteFactory extends Factory
{
    protected $model = CategoriaCliente::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->words(2, true), // ej: "Clinica Premium"
            'descripcion' => fake()->optional()->sentence(),
        ];
    }
}
