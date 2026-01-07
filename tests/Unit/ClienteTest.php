<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\CategoriaCliente;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_crea_cliente_valido(): void
    {
        $cliente = Cliente::factory()->create();

        $this->assertNotNull($cliente->id);
        $this->assertNotEmpty($cliente->nombre);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
        ]);
    }

    public function test_cliente_pertenece_a_una_categoria(): void
    {
        $categoria = CategoriaCliente::factory()->create();

        $cliente = Cliente::factory()->create([
            'categoria_id' => $categoria->id,
        ]);

        $this->assertEquals($categoria->id, $cliente->categoria->id);
    }

    public function test_casts_lat_long_y_activo(): void
    {
        $cliente = Cliente::factory()->create([
            'latitud' => -17.3895000,
            'longitud' => -66.1568000,
            'activo' => true,
        ]);

        $this->assertIsFloat($cliente->latitud);
        $this->assertIsFloat($cliente->longitud);
        $this->assertIsBool($cliente->activo);
        $this->assertTrue($cliente->activo);
    }

    public function test_se_puede_actualizar_cliente(): void
    {
        $cliente = Cliente::factory()->create([
            'nombre' => 'Cliente 1',
            'activo' => true,
        ]);

        $cliente->update([
            'nombre' => 'Cliente Actualizado',
            'activo' => false,
        ]);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => 'Cliente Actualizado',
            'activo' => false,
        ]);
    }

    public function test_nombre_es_requerido_a_nivel_bd(): void
    {
        $this->expectException(QueryException::class);

        Cliente::query()->create([
            'nombre' => null,
            'activo' => true,
        ]);
    }

    public function test_relaciones_visitas_y_rutas_existiran_cuando_se_cree_su_modelo(): void
    {
        $cliente = Cliente::factory()->create();

        // Evita fallas si aún no creaste Ruta/Visita en tu proyecto.
        if (class_exists(\App\Models\Visita::class)) {
            $this->assertTrue(method_exists($cliente, 'visitas'));
        }

        if (class_exists(\App\Models\Ruta::class)) {
            $this->assertTrue(method_exists($cliente, 'rutas'));
        }

        $this->assertTrue(method_exists($cliente, 'categoria'));
    }
}
