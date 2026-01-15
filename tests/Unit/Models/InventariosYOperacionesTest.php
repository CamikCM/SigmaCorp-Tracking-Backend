<?php

namespace Tests\Unit\Models;

use App\Models\InventarioSucursalMuestra;
use App\Models\InventarioVisitadorMuestra;
use App\Models\MuestraMedica;
use App\Models\Operacion;
use App\Models\OperacionItem;
use App\Models\Persona;
use App\Models\Sucursal;
use App\Models\VisitadorMedico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventariosYOperacionesTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventarios_y_operaciones_con_items(): void
    {
        $sucursal = Sucursal::factory()->create();
        $muestra1 = MuestraMedica::factory()->create();
        $muestra2 = MuestraMedica::factory()->create();

        $visitador = VisitadorMedico::factory()->create(['sucursal_id' => $sucursal->id]);

        InventarioSucursalMuestra::factory()->create([
            'sucursal_id' => $sucursal->id,
            'muestra_medica_id' => $muestra1->id,
            'cantidad' => 100,
        ]);

        InventarioVisitadorMuestra::factory()->create([
            'visitador_medico_id' => $visitador->id,
            'muestra_medica_id' => $muestra1->id,
            'cantidad' => 10,
        ]);

        $emitePersona = Persona::factory()->create();

        $operacion = Operacion::factory()->create([
            'emite_persona_id' => $emitePersona->id,
            'recibe_visitador_medico_id' => $visitador->id,
            'sucursal_id' => $sucursal->id,
            'tipo' => 'transferencia',
            'estado' => 'confirmada',
        ]);

        OperacionItem::factory()->create([
            'operacion_id' => $operacion->id,
            'muestra_medica_id' => $muestra1->id,
            'cantidad' => 5,
        ]);

        OperacionItem::factory()->create([
            'operacion_id' => $operacion->id,
            'muestra_medica_id' => $muestra2->id,
            'cantidad' => 7,
        ]);

        $operacion->refresh();

        $this->assertEquals(2, $operacion->items()->count());
        $this->assertEquals($emitePersona->id, $operacion->emitePersona->id);
        $this->assertEquals($visitador->id, $operacion->recibeVisitadorMedico->id);
    }
}
