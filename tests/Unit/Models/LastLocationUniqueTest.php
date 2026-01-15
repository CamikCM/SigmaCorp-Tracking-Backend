<?php

namespace Tests\Unit\Models;

use App\Models\LastLocation;
use App\Models\VisitadorMedico;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LastLocationUniqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_last_location_es_unica_por_visitador(): void
    {
        $visitador = VisitadorMedico::factory()->create();

        LastLocation::factory()->create([
            'visitador_medico_id' => $visitador->id,
        ]);

        $this->expectException(QueryException::class);

        LastLocation::factory()->create([
            'visitador_medico_id' => $visitador->id,
        ]);
    }
}
