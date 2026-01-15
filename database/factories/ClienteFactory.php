<?php

namespace Database\Factories;

use App\Models\CategoriaCliente;
use App\Models\Cliente;
use App\Models\EspecialidadMedica;
use App\Models\Persona;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'persona_id' => Persona::factory(),
            'codigo' => $this->faker->unique()->bothify('C-####'),
            'tipo_cliente' => $this->faker->randomElement(['MEDICO', 'FARMACIA', 'INSTITUCION']),
            'categoria_id' => CategoriaCliente::factory(),
            'sucursal_id' => Sucursal::factory(),
            'especialidad_id' => EspecialidadMedica::factory(),
            'activo' => true,
        ];
    }
}
