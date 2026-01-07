<?php

namespace Database\Factories;

use App\Models\Ruta;
use App\Models\Cliente;
use App\Models\RutaCliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RutaCliente>
 */
class RutaClienteFactory extends Factory
{
    protected $model = RutaCliente::class;

    public function definition(): array
    {
        return [
            'ruta_id' => Ruta::factory(),
            'cliente_id' => Cliente::factory(),
            'orden' => $this->faker->numberBetween(1, 50),
        ];
    }
}
