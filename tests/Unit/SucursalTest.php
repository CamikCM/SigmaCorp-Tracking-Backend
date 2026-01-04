<?php

namespace Tests\Unit;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SucursalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Para que las FK funcionen en SQLite (si tu entorno de tests usa sqlite)
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function test_model_usa_la_tabla_correcta(): void
    {
        $this->assertSame('sucursales', (new Sucursal)->getTable());
    }

    public function test_factory_crea_registro_valido(): void
    {
        $sucursal = Sucursal::factory()->create();

        $this->assertNotNull($sucursal->id);
        $this->assertNotEmpty($sucursal->nombre);
        $this->assertDatabaseHas('sucursales', [
            'id' => $sucursal->id,
            'nombre' => $sucursal->nombre,
        ]);
    }

    public function test_se_puede_crear_por_mass_assignment_solo_con_fillable(): void
    {
        $sucursal = Sucursal::create([
            'nombre' => 'Sigma Cochabamba',
            'ciudad' => 'Cochabamba',
            'direccion' => 'Av. América #123',
        ]);

        $this->assertDatabaseHas('sucursales', [
            'id' => $sucursal->id,
            'nombre' => 'Sigma Cochabamba',
            'ciudad' => 'Cochabamba',
        ]);
    }

    public function test_se_puede_actualizar_una_sucursal(): void
    {
        $sucursal = Sucursal::factory()->create([
            'nombre' => 'Sigma CBBA',
        ]);

        $sucursal->update([
            'nombre' => 'Sigma Cochabamba Central',
            'direccion' => 'Final Av. Blanco Galindo',
        ]);

        $this->assertDatabaseHas('sucursales', [
            'id' => $sucursal->id,
            'nombre' => 'Sigma Cochabamba Central',
            'direccion' => 'Final Av. Blanco Galindo',
        ]);
    }

    public function test_relacion_usuarios_es_hasMany_y_funciona(): void
    {
        $sucursal = Sucursal::factory()->create();

        $this->assertInstanceOf(HasMany::class, $sucursal->usuarios());

        User::factory()->count(3)->create([
            'sucursal_id' => $sucursal->id,
        ]);

        $sucursal->refresh();
        $this->assertCount(3, $sucursal->usuarios);
    }

    public function test_eliminar_sucursal_setea_users_sucursal_id_a_null(): void
    {
        $sucursal = Sucursal::factory()->create();

        $user = User::factory()->create([
            'sucursal_id' => $sucursal->id,
        ]);

        $sucursal->delete();

        $user->refresh();
        $this->assertNull($user->sucursal_id);
    }

    public function test_nombre_es_obligatorio_a_nivel_bd(): void
    {
        $this->expectException(QueryException::class);

        // nombre no es nullable en la migración
        Sucursal::query()->create([
            'nombre' => null,
            'ciudad' => 'Cochabamba',
        ]);
    }
}
