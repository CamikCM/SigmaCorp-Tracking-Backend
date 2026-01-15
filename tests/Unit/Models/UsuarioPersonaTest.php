<?php

namespace Tests\Unit\Models;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioPersonaTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pertenece_a_persona_y_persona_tiene_usuario(): void
    {
        $usuario = Usuario::factory()->create();

        $this->assertNotNull($usuario->persona);
        $this->assertEquals($usuario->persona_id, $usuario->persona->id);

        $this->assertNotNull($usuario->persona->usuario);
        $this->assertEquals($usuario->id, $usuario->persona->usuario->id);
    }
}
