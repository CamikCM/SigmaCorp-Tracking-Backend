<?php

namespace Tests\Unit;

use App\Models\InventarioVisitadorMuestra;
use App\Models\MuestraMedica;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InventarioVisitadorMuestraTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Para que las FK funcionen si tus tests usan sqlite
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function test_factory_crea_registro_valido(): void
    {
        $inv = InventarioVisitadorMuestra::factory()->create();

        $this->assertNotNull($inv->id);
        $this->assertIsInt($inv->cantidad);

        $this->assertDatabaseHas('inventarios_visitadores_muestras', [
            'id' => $inv->id,
            'usuario_id' => $inv->usuario_id,
            'muestra_medica_id' => $inv->muestra_medica_id,
        ]);
    }

    public function test_relaciones_son_belongsTo(): void
    {
        $inv = InventarioVisitadorMuestra::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $inv->usuario());
        $this->assertInstanceOf(BelongsTo::class, $inv->muestraMedica());

        $this->assertInstanceOf(User::class, $inv->usuario);
        $this->assertInstanceOf(MuestraMedica::class, $inv->muestraMedica);
    }

    public function test_unique_usuario_y_muestra_medica(): void
    {
        $user = User::factory()->create();
        $muestra = MuestraMedica::factory()->create();

        InventarioVisitadorMuestra::create([
            'usuario_id' => $user->id,
            'muestra_medica_id' => $muestra->id,
            'cantidad' => 10,
        ]);

        $this->expectException(QueryException::class);

        // Debe fallar por unique(usuario_id, muestra_medica_id)
        InventarioVisitadorMuestra::create([
            'usuario_id' => $user->id,
            'muestra_medica_id' => $muestra->id,
            'cantidad' => 5,
        ]);
    }

    public function test_default_cantidad_es_cero(): void
    {
        $user = User::factory()->create();
        $muestra = MuestraMedica::factory()->create();

        $inv = InventarioVisitadorMuestra::create([
            'usuario_id' => $user->id,
            'muestra_medica_id' => $muestra->id,
            // sin cantidad
        ]);

        $inv->refresh();
        $this->assertSame(0, (int) $inv->cantidad);
    }

    public function test_cascade_delete_al_eliminar_usuario(): void
    {
        $inv = InventarioVisitadorMuestra::factory()->create();
        $id = $inv->id;

        $inv->usuario->delete();

        $this->assertDatabaseMissing('inventarios_visitadores_muestras', [
            'id' => $id,
        ]);
    }

    public function test_restrict_delete_al_eliminar_muestra_medica(): void
    {
        $inv = InventarioVisitadorMuestra::factory()->create();
        $muestra = $inv->muestraMedica;

        $this->expectException(QueryException::class);

        // Debe fallar porque la FK está en restrictOnDelete()
        $muestra->delete();
    }
}
