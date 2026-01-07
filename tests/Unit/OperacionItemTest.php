<?php

namespace Tests\Unit;

use App\Models\MuestraMedica;
use App\Models\Operacion;
use App\Models\OperacionItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OperacionItemTest extends TestCase
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

    public function test_factory_crea_item_valido(): void
    {
        $item = OperacionItem::factory()->create();

        $this->assertDatabaseHas('operaciones_items', [
            'id' => $item->id,
            'operacion_id' => $item->operacion_id,
            'muestra_medica_id' => $item->muestra_medica_id,
            'cantidad' => $item->cantidad,
        ]);
    }

    public function test_relaciones_son_belongsTo(): void
    {
        $item = OperacionItem::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $item->operacion());
        $this->assertInstanceOf(BelongsTo::class, $item->muestraMedica());
    }

    public function test_fk_son_obligatorias(): void
    {
        $this->expectException(QueryException::class);

        OperacionItem::create([
            'operacion_id' => null,
            'muestra_medica_id' => null,
            'cantidad' => 10,
        ]);
    }

    public function test_al_borrar_operacion_se_eliminan_sus_items(): void
    {
        $op = Operacion::factory()->create();

        $item = OperacionItem::factory()->create([
            'operacion_id' => $op->id,
        ]);

        $this->assertDatabaseHas('operaciones_items', ['id' => $item->id]);

        $op->delete();

        $this->assertDatabaseMissing('operaciones_items', ['id' => $item->id]);
    }

    public function test_no_se_puede_borrar_muestra_medica_si_esta_usada_en_items(): void
    {
        $muestra = MuestraMedica::factory()->create();

        OperacionItem::factory()->create([
            'muestra_medica_id' => $muestra->id,
        ]);

        $this->expectException(QueryException::class);
        $muestra->delete();
    }
}
