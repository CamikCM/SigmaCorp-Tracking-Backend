<?php

namespace Tests\Unit\Models;

use App\Models\AsignacionRuta;
use App\Models\Ruta;
use App\Models\VisitadorMedico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RutasAsignacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_asignacion_de_ruta_a_visitador(): void
    {
        $ruta = Ruta::factory()->create();
        $visitador = VisitadorMedico::factory()->create();

        $asignacion = AsignacionRuta::factory()->create([
            'ruta_id' => $ruta->id,
            'visitador_medico_id' => $visitador->id,
            'activo' => true,
        ]);

        $visitador->refresh();
        $ruta->refresh();

        $this->assertTrue($visitador->rutas->contains($ruta));
        $this->assertTrue($ruta->visitadoresMedicos->contains($visitador));
        $this->assertEquals(true, (bool) $asignacion->activo);
    }
}
