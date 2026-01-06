<?php

namespace Tests\Unit;

use App\Models\MuestraMedica;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MuestraMedicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_crea_registro_valido(): void
    {
        $muestra = MuestraMedica::factory()->create();

        $this->assertNotNull($muestra->id);
        $this->assertNotEmpty($muestra->nombre);

        $this->assertDatabaseHas('muestras_medicas', [
            'id' => $muestra->id,
            'nombre' => $muestra->nombre,
        ]);
    }

    public function test_se_puede_crear_por_mass_assignment(): void
    {
        $muestra = MuestraMedica::create([
            'nombre' => 'Amoxicilina 500mg',
            'tipo' => 'Cápsula',
            'descripcion' => 'Muestra para promoción médica.',
            'activa' => true,
        ]);

        $this->assertDatabaseHas('muestras_medicas', [
            'id' => $muestra->id,
            'nombre' => 'Amoxicilina 500mg',
            'tipo' => 'Cápsula',
        ]);
    }

    public function test_se_puede_actualizar(): void
    {
        $muestra = MuestraMedica::factory()->create(['nombre' => 'Paracetamol']);

        $muestra->update([
            'nombre' => 'Paracetamol 500mg',
            'activa' => false,
        ]);

        $muestra->refresh();

        $this->assertSame('Paracetamol 500mg', $muestra->nombre);
        $this->assertFalse($muestra->activa);
        $this->assertIsBool($muestra->activa);
    }

    public function test_cast_activa_es_boolean(): void
    {
        $muestra = MuestraMedica::factory()->inactiva()->create();

        $this->assertIsBool($muestra->activa);
        $this->assertFalse($muestra->activa);
    }

    public function test_nombre_es_obligatorio_a_nivel_bd(): void
    {
        $this->expectException(QueryException::class);

        MuestraMedica::query()->create([
            'nombre' => null, // NOT NULL
        ]);
    }

/*    public function test_relaciones_existen_y_funcionan_si_los_modelos_dependientes_ya_existen(): void
    {
        // Si todavía no creaste estos modelos, este test se salta sin fallar.
        $dependencias = [
            \App\Models\InventarioSucursalMuestra::class,
            \App\Models\InventarioVisitadorMuestra::class,
            \App\Models\OperacionItem::class,
        ];

        foreach ($dependencias as $dep) {
            if (!class_exists($dep)) {
                $this->markTestSkipped("Relación omitida: falta el modelo {$dep} (se creará en su issue).");
            }
        }

        $muestra = MuestraMedica::factory()->create();

        $this->assertNotNull($muestra->inventariosSucursales());
        $this->assertNotNull($muestra->inventariosVisitadores());
        $this->assertNotNull($muestra->operacionesItems());
    }
*/
}
