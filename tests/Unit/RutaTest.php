<?php

namespace Tests\Unit;

use App\Models\Ruta;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RutaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Si los tests usan sqlite, habilita FK.
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function test_factory_crea_ruta_valida(): void
    {
        $ruta = Ruta::factory()->create();

        $this->assertNotNull($ruta->id);
        $this->assertNotEmpty($ruta->nombre);
        $this->assertTrue(is_bool($ruta->activa));

        $this->assertDatabaseHas('rutas', [
            'id' => $ruta->id,
            'nombre' => $ruta->nombre,
            'sucursal_id' => $ruta->sucursal_id,
        ]);
    }

    public function test_cast_activa_es_boolean(): void
    {
        $ruta = Ruta::factory()->create(['activa' => 1]);
        $this->assertIsBool($ruta->activa);
        $this->assertTrue($ruta->activa);

        $ruta2 = Ruta::factory()->inactiva()->create();
        $this->assertIsBool($ruta2->activa);
        $this->assertFalse($ruta2->activa);
    }

    public function test_relacion_sucursal_es_belongsTo_y_funciona(): void
    {
        $sucursal = Sucursal::factory()->create();
        $ruta = Ruta::factory()->create(['sucursal_id' => $sucursal->id]);

        $this->assertInstanceOf(BelongsTo::class, $ruta->sucursal());
        $this->assertSame($sucursal->id, $ruta->sucursal->id);
    }

    public function test_se_puede_crear_por_mass_assignment(): void
    {
        $sucursal = Sucursal::factory()->create();

        $ruta = Ruta::create([
            'nombre' => 'Ruta Norte',
            'descripcion' => 'Zona norte - clientes VIP',
            'sucursal_id' => $sucursal->id,
            'activa' => true,
        ]);

        $this->assertDatabaseHas('rutas', [
            'id' => $ruta->id,
            'nombre' => 'Ruta Norte',
            'sucursal_id' => $sucursal->id,
        ]);
    }

    public function test_update_ruta(): void
    {
        $ruta = Ruta::factory()->create(['nombre' => 'Ruta A']);

        $ruta->update([
            'nombre' => 'Ruta A (Actualizada)',
            'activa' => false,
        ]);

        $this->assertDatabaseHas('rutas', [
            'id' => $ruta->id,
            'nombre' => 'Ruta A (Actualizada)',
            'activa' => false,
        ]);
    }

    public function test_no_permite_sucursal_id_inexistente(): void
    {
        $this->expectException(QueryException::class);

        Ruta::create([
            'nombre' => 'Ruta inválida',
            'descripcion' => null,
            'sucursal_id' => 999999, // no existe
            'activa' => true,
        ]);
    }

    public function test_no_permite_nombre_null(): void
    {
        $this->expectException(QueryException::class);

        $sucursal = Sucursal::factory()->create();

        Ruta::create([
            'nombre' => null,
            'descripcion' => null,
            'sucursal_id' => $sucursal->id,
            'activa' => true,
        ]);
    }

    public function test_no_se_puede_eliminar_sucursal_si_tiene_rutas_restrict(): void
    {
        // Esto valida el restrictOnDelete() de la FK
        $this->expectException(QueryException::class);

        $sucursal = Sucursal::factory()->create();
        Ruta::factory()->create(['sucursal_id' => $sucursal->id]);

        $sucursal->delete(); // debería fallar por RESTRICT
    }
}
