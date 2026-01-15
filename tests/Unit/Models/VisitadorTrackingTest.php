<?php

namespace Tests\Unit\Models;

use App\Models\EstadoUser;
use App\Models\Jornada;
use App\Models\LastLocation;
use App\Models\Location;
use App\Models\Sucursal;
use App\Models\VisitadorEstadoTracking;
use App\Models\VisitadorMedico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitadorTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_relaciones_de_tracking_del_visitador_medico(): void
    {
        $off = EstadoUser::factory()->off()->create();
        $on  = EstadoUser::factory()->on()->create();
        $sucursal = Sucursal::factory()->create();

        $visitador = VisitadorMedico::factory()->create([
            'sucursal_id' => $sucursal->id,
            'estado_user_id' => $off->id,
        ]);

        $jornada = Jornada::factory()->create([
            'visitador_medico_id' => $visitador->id,
        ]);

        $location = Location::factory()->create([
            'visitador_medico_id' => $visitador->id,
            'jornada_id' => $jornada->id,
        ]);

        $last = LastLocation::factory()->create([
            'visitador_medico_id' => $visitador->id,
        ]);

        $evento = VisitadorEstadoTracking::factory()->create([
            'visitador_medico_id' => $visitador->id,
            'jornada_id' => $jornada->id,
            'estado_user_id' => $on->id,
            'tipo_marcado' => 'manual',
        ]);

        $this->assertEquals($visitador->id, $jornada->visitadorMedico->id);
        $this->assertTrue($visitador->locations->contains($location));
        $this->assertEquals($visitador->id, $last->visitadorMedico->id);
        $this->assertTrue($visitador->eventosEstado->contains($evento));
        $this->assertEquals($sucursal->id, $visitador->sucursal->id);
    }
}
