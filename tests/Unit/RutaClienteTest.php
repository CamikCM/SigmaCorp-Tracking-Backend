<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\Ruta;
use App\Models\RutaCliente;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RutaClienteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Para que FK funcione si el entorno de test usa sqlite
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function test_crea_ruta_cliente_con_factory(): void
    {
        $pivot = RutaCliente::factory()->create();

        $this->assertNotNull($pivot->id);
        $this->assertDatabaseHas('ruta_clientes', [
            'id' => $pivot->id,
            'ruta_id' => $pivot->ruta_id,
            'cliente_id' => $pivot->cliente_id,
        ]);
    }

    public function test_relaciones_son_belongs_to(): void
    {
        $pivot = RutaCliente::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $pivot->ruta());
        $this->assertInstanceOf(BelongsTo::class, $pivot->cliente());

        $this->assertInstanceOf(Ruta::class, $pivot->ruta);
        $this->assertInstanceOf(Cliente::class, $pivot->cliente);
    }

    public function test_default_orden_es_1_cuando_no_se_envia(): void
    {
        $ruta = Ruta::factory()->create();
        $cliente = Cliente::factory()->create();

        $pivot = RutaCliente::create([
            'ruta_id' => $ruta->id,
            'cliente_id' => $cliente->id,
            // no enviamos 'orden'
        ]);

        $pivot->refresh();
        $this->assertSame(1, (int) $pivot->orden);
    }

    public function test_update_de_orden_funciona(): void
    {
        $pivot = RutaCliente::factory()->create(['orden' => 1]);

        $pivot->update(['orden' => 10]);

        $this->assertDatabaseHas('ruta_clientes', [
            'id' => $pivot->id,
            'orden' => 10,
        ]);
    }

    public function test_unique_ruta_id_cliente_id_no_permite_duplicados(): void
    {
        $this->expectException(QueryException::class);

        $ruta = Ruta::factory()->create();
        $cliente = Cliente::factory()->create();

        RutaCliente::create([
            'ruta_id' => $ruta->id,
            'cliente_id' => $cliente->id,
            'orden' => 1,
        ]);

        // duplicado debe fallar por unique
        RutaCliente::create([
            'ruta_id' => $ruta->id,
            'cliente_id' => $cliente->id,
            'orden' => 2,
        ]);
    }

    public function test_al_eliminar_ruta_o_cliente_se_elimina_el_pivot_por_cascade(): void
    {
        $ruta = Ruta::factory()->create();
        $cliente = Cliente::factory()->create();

        $pivot = RutaCliente::create([
            'ruta_id' => $ruta->id,
            'cliente_id' => $cliente->id,
            'orden' => 1,
        ]);

        $this->assertDatabaseHas('ruta_clientes', ['id' => $pivot->id]);

        // Al borrar ruta -> cascade elimina pivots
        $ruta->delete();

        $this->assertDatabaseMissing('ruta_clientes', ['id' => $pivot->id]);
    }
}
