<?php

namespace Database\Factories;

use App\Models\Operacion;
use App\Models\Persona;
use App\Models\Sucursal;
use App\Models\VisitadorMedico;
use Illuminate\Database\Eloquent\Factories\Factory;

class OperacionFactory extends Factory
{
    protected $model = Operacion::class;

    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['ENTREGA', 'DEVOLUCION', 'AJUSTE']),
            'estado' => $this->faker->randomElement(['PENDIENTE', 'CONFIRMADA', 'ANULADA']),
            'fecha' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'emite_persona_id' => Persona::factory(),
            'recibe_visitador_medico_id' => $this->faker->boolean(70) ? VisitadorMedico::factory() : null,
            'sucursal_id' => $this->faker->boolean(70) ? Sucursal::factory() : null,
            'observaciones' => $this->faker->optional()->sentence(),
        ];
    }
}
