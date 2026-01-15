<?php

namespace Tests\Feature\Auth;

use App\Models\EstadoUser;
use App\Models\Sucursal;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_register_creates_user_and_visitador_profile(): void
    {
        // El endpoint de registro asume que existe el estado OFF.
        EstadoUser::factory()->off()->create();
        $sucursal = Sucursal::factory()->create();

        $response = $this->postJson('/api/register', [
            'name' => 'Visitador Test',
            'email' => 'visitador@test.com',
            'password' => 'secret123',
            'tipo' => 'visitador_medico',
            'sucursal_id' => $sucursal->id,
        ]);

        $response
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json) =>
                $json->hasAll(['message', 'token'])
                    ->has('user', fn (AssertableJson $u) =>
                        $u->hasAll(['id', 'name', 'email'])
                    )
            );

        $this->assertDatabaseHas('users', [
            'email' => 'visitador@test.com',
        ]);

        $user = Usuario::where('email', 'visitador@test.com')->firstOrFail();

        $this->assertDatabaseHas('persona', [
            'id' => $user->persona_id,
        ]);

        $this->assertDatabaseHas('visitadores_medicos', [
            'persona_id' => $user->persona_id,
            'sucursal_id' => $sucursal->id,
        ]);
    }
}
