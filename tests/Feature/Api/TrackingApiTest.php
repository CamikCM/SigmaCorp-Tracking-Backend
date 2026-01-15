<?php

namespace Tests\Feature\Api;

use App\Models\EstadoUser;
use App\Models\Usuario;
use App\Models\VisitadorMedico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrackingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_on_off_updates_estado_and_creates_tracking_events(): void
    {
        $estadoOff = EstadoUser::factory()->off()->create();
        $estadoOn = EstadoUser::factory()->on()->create();

        $visitador = VisitadorMedico::factory()->create([
            'estado_user_id' => $estadoOff->id,
        ]);

        $user = Usuario::query()->where('persona_id', $visitador->persona_id)->firstOrFail();

        Sanctum::actingAs($user);

        // ON
        $this->postJson('/api/tracking/on')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'state' => 'ON',
            ]);

        $this->assertDatabaseHas('visitadores_medicos', [
            'id' => $visitador->id,
            'estado_user_id' => $estadoOn->id,
        ]);

        $this->assertDatabaseCount('visitador_estado_tracking', 1);

        // OFF
        $this->postJson('/api/tracking/off')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'state' => 'OFF',
            ]);

        $this->assertDatabaseHas('visitadores_medicos', [
            'id' => $visitador->id,
            'estado_user_id' => $estadoOff->id,
        ]);

        $this->assertDatabaseCount('visitador_estado_tracking', 2);
    }
}
