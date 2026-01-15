<?php

namespace Tests\Unit\Models;

use App\Models\Jornada;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JornadaEstadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_jornada_tiene_locations_y_estado_por_defecto(): void
    {
        $jornada = Jornada::factory()->create([
            'estado' => 'abierta',
        ]);

        Location::factory()->count(3)->create([
            'visitador_medico_id' => $jornada->visitador_medico_id,
            'jornada_id' => $jornada->id,
        ]);

        $this->assertSame('abierta', $jornada->estado);
        $this->assertCount(3, $jornada->locations);
    }
}
