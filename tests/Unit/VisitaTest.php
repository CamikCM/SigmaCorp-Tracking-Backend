<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\Jornada;
use App\Models\Ruta;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VisitaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Para SQLite en tests (si aplica)
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function test_factory_crea_visita_valida(): void
    {
        $visita = Visita::factory()->create();

        $this->assertDatabaseHas('visitas', [
            'id' => $visita->id,
            'usuario_id' => $visita->usuario_id,
            'cliente_id' => $visita->cliente_id,
            'jornada_id' => $visita->jornada_id,
        ]);
    }

    public function test_relaciones_son_belongsTo(): void
    {
        $visita = Visita::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $visita->usuario());
        $this->assertInstanceOf(BelongsTo::class, $visita->cliente());
        $this->assertInstanceOf(BelongsTo::class, $visita->jornada());
        $this->assertInstanceOf(BelongsTo::class, $visita->ruta());
    }

    public function test_se_puede_crear_visita_sin_ruta(): void
    {
        $usuario = User::factory()->create();
        $cliente = Cliente::factory()->create();
        $jornada = Jornada::factory()->create(['usuario_id' => $usuario->id]);

        $visita = Visita::create([
            'usuario_id' => $usuario->id,
            'cliente_id' => $cliente->id,
            'jornada_id' => $jornada->id,
            'ruta_id' => null,
            'check_in' => now(),
            'check_out' => null,
            'latitud' => -17.39,
            'longitud' => -66.16,
            'notas' => 'Visita sin ruta asignada',
        ]);

        $this->assertDatabaseHas('visitas', [
            'id' => $visita->id,
            'ruta_id' => null,
        ]);
    }

    public function test_casts_datetime_y_float_funcionan(): void
    {
        $visita = Visita::factory()->soloCheckIn()->create([
            'latitud' => '-17.3900000',
            'longitud' => '-66.1600000',
        ]);

        $this->assertIsFloat($visita->latitud);
        $this->assertIsFloat($visita->longitud);
        $this->assertNotNull($visita->check_in);
        $this->assertNull($visita->check_out);
    }

    public function test_fk_obligatorias_no_permiten_null(): void
    {
        $this->expectException(QueryException::class);

        Visita::create([
            'usuario_id' => null,
            'cliente_id' => null,
            'jornada_id' => null,
        ]);
    }

    public function test_borrado_de_jornada_cascade_elimina_visitas(): void
    {
        $usuario = User::factory()->create();
        $cliente = Cliente::factory()->create();
        $ruta = Ruta::factory()->create();
        $jornada = Jornada::factory()->create(['usuario_id' => $usuario->id]);

        $visita = Visita::factory()->create([
            'usuario_id' => $usuario->id,
            'cliente_id' => $cliente->id,
            'jornada_id' => $jornada->id,
            'ruta_id' => $ruta->id,
        ]);

        $this->assertDatabaseHas('visitas', ['id' => $visita->id]);

        $jornada->delete();

        $this->assertDatabaseMissing('visitas', ['id' => $visita->id]);
    }

    public function test_borrado_de_ruta_setea_ruta_id_a_null(): void
    {
        $usuario = User::factory()->create();
        $cliente = Cliente::factory()->create();
        $ruta = Ruta::factory()->create();
        $jornada = Jornada::factory()->create(['usuario_id' => $usuario->id]);

        $visita = Visita::factory()->create([
            'usuario_id' => $usuario->id,
            'cliente_id' => $cliente->id,
            'jornada_id' => $jornada->id,
            'ruta_id' => $ruta->id,
        ]);

        $ruta->delete();

        $visita->refresh();
        $this->assertNull($visita->ruta_id);
    }
}
