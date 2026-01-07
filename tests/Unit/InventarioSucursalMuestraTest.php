<?php

namespace Tests\Unit;

use App\Models\InventarioSucursalMuestra;
use App\Models\MuestraMedica;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InventarioSucursalMuestraTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Para que funcionen FK en SQLite (si tu entorno de tests usa sqlite)
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function test_factory_crea_registro_valido(): void
    {
        $inv = InventarioSucursalMuestra::factory()->create();

        $this->assertNotNull($inv->id);
        $this->assertIsInt($inv->cantidad);

        $this->assertDatabaseHas('inventarios_sucursales_muestras', [
            'id' => $inv->id,
            'sucursal_id' => $inv->sucursal_id,
            'muestra_medica_id' => $inv->muestra_medica_id,
        ]);
    }

    public function test_relaciones_son_belongsTo_y_funcionan(): void
    {
        $inv = InventarioSucursalMuestra::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $inv->sucursal());
        $this->assertInstanceOf(BelongsTo::class, $inv->muestraMedica());

        $this->assertNotNull($inv->sucursal);
        $this->assertNotNull($inv->muestraMedica);
    }

    public function test_se_puede_actualizar_cantidad(): void
    {
        $inv = InventarioSucursalMuestra::factory()->create(['cantidad' => 10]);

        $inv->update(['cantidad' => 25]);

        $this->assertDatabaseHas('inventarios_sucursales_muestras', [
            'id' => $inv->id,
            'cantidad' => 25,
        ]);
    }

    public function test_unique_sucursal_muestra_no_permiteduplicados(): void
    {
        $sucursal = Sucursal::factory()->create();
        $muestra = MuestraMedica::factory()->create();

        InventarioSucursalMuestra::create([
            'sucursal_id' => $sucursal->id,
            'muestra_medica_id' => $muestra->id,
            'cantidad' => 1,
        ]);

        $this->expectException(QueryException::class);

        InventarioSucursalMuestra::create([
            'sucursal_id' => $sucursal->id,
            'muestra_medica_id' => $muestra->id,
            'cantidad' => 2,
        ]);
    }

    public function test_eliminar_sucursal_elimina_inventario_por_cascade(): void
    {
        $inv = InventarioSucursalMuestra::factory()->create();
        $invId = $inv->id;

        $inv->sucursal->delete();

        $this->assertDatabaseMissing('inventarios_sucursales_muestras', [
            'id' => $invId,
        ]);
    }

    public function test_no_permitedelete_muestra_medica_si_existe_inventario_restrict(): void
    {
        $inv = InventarioSucursalMuestra::factory()->create();

        $this->expectException(QueryException::class);

        $inv->muestraMedica->delete();
    }
}
