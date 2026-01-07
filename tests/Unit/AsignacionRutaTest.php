<?php

namespace Tests\Unit;

use App\Models\AsignacionRuta;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AsignacionRutaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Para que FK funcione en SQLite (si tu entorno de test usa sqlite)
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function test_factory_crea_asignacion_valida(): void
    {
        $asignacion = AsignacionRuta::factory()->create();

        $this->assertNotNull($asignacion->id);
        $this->assertNotNull($asignacion->ruta_id);
        $this->assertNotNull($asignacion->usuario_id);
        $this->assertTrue($asignacion->activa);

        $this->assertDatabaseHas('asignaciones_rutas', [
            'id' => $asignacion->id,
            'ruta_id' => $asignacion->ruta_id,
            'usuario_id' => $asignacion->usuario_id,
        ]);
    }

    public function test_relaciones_son_belongsTo(): void
    {
        $asignacion = AsignacionRuta::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $asignacion->ruta());
        $this->assertInstanceOf(BelongsTo::class, $asignacion->usuario());

        $this->assertInstanceOf(Ruta::class, $asignacion->ruta);
        $this->assertInstanceOf(User::class, $asignacion->usuario);
    }

    public function test_unique_ruta_usuario_fecha_inicio(): void
    {
        $ruta = Ruta::factory()->create();
        $user = User::factory()->create();
        $fecha = '2026-01-07';

        AsignacionRuta::create([
            'ruta_id' => $ruta->id,
            'usuario_id' => $user->id,
            'fecha_inicio' => $fecha,
            'fecha_fin' => null,
            'activa' => true,
        ]);

        $this->expectException(QueryException::class);

        // Duplicado exacto => debe fallar por unique
        AsignacionRuta::create([
            'ruta_id' => $ruta->id,
            'usuario_id' => $user->id,
            'fecha_inicio' => $fecha,
            'fecha_fin' => null,
            'activa' => true,
        ]);
    }

    public function test_se_puede_actualizar_fecha_fin_y_activa(): void
    {
        $asignacion = AsignacionRuta::factory()->create([
            'fecha_fin' => null,
            'activa' => true,
        ]);

        $asignacion->update([
            'fecha_fin' => '2026-02-01',
            'activa' => false,
        ]);

        $asignacion->refresh();

        $this->assertSame('2026-02-01', $asignacion->fecha_fin->format('Y-m-d'));
        $this->assertFalse($asignacion->activa);
    }

    public function test_cascade_delete_si_se_elimina_la_ruta(): void
    {
        $asignacion = AsignacionRuta::factory()->create();
        $id = $asignacion->id;

        $asignacion->ruta->delete();

        $this->assertDatabaseMissing('asignaciones_rutas', ['id' => $id]);
    }

    public function test_cascade_delete_si_se_elimina_el_usuario(): void
    {
        $asignacion = AsignacionRuta::factory()->create();
        $id = $asignacion->id;

        $asignacion->usuario->delete();

        $this->assertDatabaseMissing('asignaciones_rutas', ['id' => $id]);
    }
}
