<?php

namespace Tests\Unit\Models;

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientePersonaTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_es_hijo_de_persona(): void
    {
        $cliente = Cliente::factory()->create();

        $this->assertNotNull($cliente->persona);
        $this->assertEquals($cliente->persona_id, $cliente->persona->id);

        $this->assertNotNull($cliente->persona->cliente);
        $this->assertEquals($cliente->id, $cliente->persona->cliente->id);
    }
}
