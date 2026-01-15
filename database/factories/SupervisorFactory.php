<?php

namespace Database\Factories;

use App\Models\Persona;
use App\Models\Sucursal;
use App\Models\Supervisor;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupervisorFactory extends Factory
{
    protected $model = Supervisor::class;

    public function definition(): array
    {
        return [
            'persona_id' => Persona::factory(),
            'sucursal_id' => Sucursal::factory(),
            'cargo' => $this->faker->optional()->jobTitle(),
            'activo' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Supervisor $supervisor) {
            Usuario::factory()->create(['persona_id' => $supervisor->persona_id]);
        });
    }
}
