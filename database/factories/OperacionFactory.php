<?php

namespace Database\Factories;

use App\Models\Operacion;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Operacion>
 */
class OperacionFactory extends Factory
{
    protected $model = Operacion::class;

    public function definition(): array
    {
        return [
            'fecha_registro' => fake()->dateTimeBetween('-7 days', 'now'),
            'tipo' => fake()->randomElement(['entrega', 'devolucion', 'ajuste']),
            'comprobante' => fake()->optional()->bothify('COMP-####-??'),
            'sucursal_id' => Sucursal::factory(),
            'entrega_usuario_id' => User::factory(),
            'recibe_usuario_id' => User::factory(),
            'total_unidades' => fake()->numberBetween(0, 200),
            'observacion' => fake()->optional()->sentence(),
        ];
    }

    public function sinEntrega(): static
    {
        return $this->state(fn () => ['entrega_usuario_id' => null]);
    }

    public function sinRecibe(): static
    {
        return $this->state(fn () => ['recibe_usuario_id' => null]);
    }
}
