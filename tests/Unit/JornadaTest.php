<?php

namespace Tests\Unit;

use App\Models\Jornada;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JornadaTest extends TestCase
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

    public function test_factory_crea_jornada_con_defaults(): void
    {
        $jornada = Jornada::factory()->create();

        $this->assertNotNull($jornada->id);
        $this->assertSame('pendiente', $jornada->estado);
        $this->assertFalse($jornada->tracking_habilitado);

        $this->assertDatabaseHas('jornadas', [
            'id' => $jornada->id,
            'usuario_id' => $jornada->usuario_id,
            'estado' => 'pendiente',
        ]);
    }

    public function test_unique_usuario_id_y_fecha(): void
    {
        $user = User::factory()->create();
        $fecha = '2026-01-07';

        Jornada::factory()->create([
            'usuario_id' => $user->id,
            'fecha' => $fecha,
        ]);

        $this->expectException(QueryException::class);

        Jornada::factory()->create([
            'usuario_id' => $user->id,
            'fecha' => $fecha,
        ]);
    }

    public function test_relacion_usuario_funciona(): void
    {
        $user = User::factory()->create();
        $jornada = Jornada::factory()->create(['usuario_id' => $user->id]);

        $this->assertTrue($jornada->usuario->is($user));
    }

    public function test_puede_actualizar_estado_y_tracking(): void
    {
        $jornada = Jornada::factory()->create();

        $jornada->update([
            'estado' => 'activa',
            'tracking_habilitado' => true,
            'inicio_real' => now(),
        ]);

        $jornada->refresh();

        $this->assertSame('activa', $jornada->estado);
        $this->assertTrue($jornada->tracking_habilitado);
        $this->assertNotNull($jornada->inicio_real);
    }

    public function test_relacion_locations_por_jornada_id(): void
    {
        $user = User::factory()->create();
        $jornada = Jornada::factory()->create(['usuario_id' => $user->id]);

        Location::factory()->count(2)->create([
            'user_id' => $user->id,
            'jornada_id' => $jornada->id,
        ]);

        $this->assertCount(2, $jornada->locations()->get());
    }

    public function test_eliminar_usuario_cascade_elimina_su_jornada(): void
    {
        $user = User::factory()->create();
        $jornada = Jornada::factory()->create(['usuario_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('jornadas', [
            'id' => $jornada->id,
        ]);
    }
}
