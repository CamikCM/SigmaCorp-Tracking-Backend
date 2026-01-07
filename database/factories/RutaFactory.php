<?php

namespace Database\Factories;

use App\Models\Ruta;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ruta>
 */
class RutaFactory extends Factory
{
    protected $model = Ruta::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Ruta ' . fake()->city(),
            'descripcion' => fake()->optional()->sentence(),
            'sucursal_id' => Sucursal::factory(),
            'activa' => true,
        ];
    }

    public function inactiva(): self
    {
        return $this->state(fn () => ['activa' => false]);
    }
}
