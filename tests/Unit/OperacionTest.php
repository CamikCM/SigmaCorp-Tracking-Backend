<?php

namespace Tests\Unit;

use App\Models\Operacion;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OperacionTest extends TestCase
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

    public function test_factory_crea_operacion_valida(): void
    {
        $op = Operacion::factory()->create();

        $this->assertDatabaseHas('operaciones', [
            'id' => $op->id,
            'tipo' => $op->tipo,
            'sucursal_id' => $op->sucursal_id,
        ]);
    }

    public function test_relaciones_estan_definidas(): void
    {
        $op = Operacion::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $op->sucursal());
        $this->assertInstanceOf(BelongsTo::class, $op->entregaUsuario());
        $this->assertInstanceOf(BelongsTo::class, $op->recibeUsuario());
        $this->assertInstanceOf(HasMany::class, $op->items());
    }

    public function test_casts_funcionan(): void
    {
        $op = Operacion::factory()->create([
            'total_unidades' => '25',
        ]);

        $this->assertIsInt($op->total_unidades);
        $this->assertNotNull($op->fecha_registro);
    }

    public function test_campos_obligatorios_no_permiten_null(): void
    {
        $this->expectException(QueryException::class);

        Operacion::create([
            'fecha_registro' => null,
            'tipo' => null,
            'sucursal_id' => null,
        ]);
    }

    public function test_al_eliminar_usuario_entrega_se_setea_a_null(): void
    {
        $sucursal = Sucursal::factory()->create();
        $entrega = User::factory()->create();
        $recibe = User::factory()->create();

        $op = Operacion::factory()->create([
            'sucursal_id' => $sucursal->id,
            'entrega_usuario_id' => $entrega->id,
            'recibe_usuario_id' => $recibe->id,
        ]);

        $entrega->delete();

        $op->refresh();
        $this->assertNull($op->entrega_usuario_id);
    }

    public function test_al_eliminar_usuario_recibe_se_setea_a_null(): void
    {
        $sucursal = Sucursal::factory()->create();
        $entrega = User::factory()->create();
        $recibe = User::factory()->create();

        $op = Operacion::factory()->create([
            'sucursal_id' => $sucursal->id,
            'entrega_usuario_id' => $entrega->id,
            'recibe_usuario_id' => $recibe->id,
        ]);

        $recibe->delete();

        $op->refresh();
        $this->assertNull($op->recibe_usuario_id);
    }
}
