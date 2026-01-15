<?php

namespace Database\Factories;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'persona_id' => Persona::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'device' => $this->faker->optional()->userAgent(),
            'is_active' => true,
            'last_login_at' => $this->faker->optional()->dateTimeBetween('-10 days', 'now'),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
