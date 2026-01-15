<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonaFactory extends Factory
{
    protected $model = Persona::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido_pat' => $this->faker->lastName(),
            'apellido_mat' => $this->faker->lastName(),
            'telefono_principal' => $this->faker->optional()->phoneNumber(),
            'telefono_secundario' => $this->faker->optional()->phoneNumber(),
            'email_personal' => $this->faker->optional()->unique()->safeEmail(),
            'direccion' => $this->faker->optional()->address(),
            'habilitado' => true,
            'carnet_identidad' => $this->faker->optional()->unique()->numerify('CI########'),
            'foto_url' => $this->faker->optional()->imageUrl(300, 300),
        ];
    }
}
