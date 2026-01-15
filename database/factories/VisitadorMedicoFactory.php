<?php

namespace Database\Factories;

use App\Models\EstadoUser;
use App\Models\Persona;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VisitadorMedico;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitadorMedicoFactory extends Factory
{
    protected $model = VisitadorMedico::class;

    public function definition(): array
    {
        return [
            'persona_id' => Persona::factory(),
            'sucursal_id' => Sucursal::factory(),
            'estado_user_id' => EstadoUser::factory()->off(),
            'codigo' => $this->faker->optional()->unique()->bothify('V-####'),
            'activo' => true,
            'last_ping_at' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (VisitadorMedico $visitador) {
            // Normalmente el visitador necesita usuario para login (mismo persona_id)
            Usuario::factory()->create(['persona_id' => $visitador->persona_id]);
        });
    }

    public function on(): static
    {
        return $this->state(fn () => ['estado_user_id' => EstadoUser::factory()->on()]);
    }
}
