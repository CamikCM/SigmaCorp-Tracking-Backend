<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\CategoriaCliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        $codigos = [null, 'AA', 'AAA', 'A', 'B'];

        return [
            'codigo' => fake()->randomElement($codigos),
            'abreviatura' => fake()->optional()->lexify('???'),
            'nombre' => fake()->name(),
            'especialidad' => fake()->optional()->randomElement([
                'Medicina General', 'Pediatría', 'Cardiología', 'Farmacia'
            ]),
            'descripcion' => fake()->optional()->sentence(),
            'direccion' => fake()->optional()->address(),

            'latitud' => fake()->optional()->latitude(-19.2, -17.0),
            'longitud' => fake()->optional()->longitude(-66.0, -64.0),

            'categoria_id' => CategoriaCliente::factory(),
            'activo' => true,
        ];
    }
}
