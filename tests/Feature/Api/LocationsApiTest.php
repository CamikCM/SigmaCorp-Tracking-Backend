<?php

namespace Tests\Feature\Api;

use App\Models\Usuario;
use App\Models\VisitadorMedico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocationsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_locations_requires_auth(): void
    {
        $this->postJson('/api/locations', [
            'latitude' => -17.3935,
            'longitude' => -66.1570,
            'recorded_at' => now()->toISOString(),
        ])->assertUnauthorized();
    }

    public function test_post_location_creates_location_and_updates_last_location(): void
    {
        $visitador = VisitadorMedico::factory()->create();
        $user = Usuario::query()->where('persona_id', $visitador->persona_id)->firstOrFail();

        Sanctum::actingAs($user);

        $payload = [
            'latitude' => -17.3935,
            'longitude' => -66.1570,
            'accuracy' => 5.3,
            'speed' => 0.0,
            'heading' => 0.0,
            'altitude' => 2600.0,
            'provider' => 'gps',
            'recorded_at' => now()->toISOString(),
        ];

        $this->postJson('/api/locations', $payload)
            ->assertCreated()
            ->assertJson([
                'success' => true,
                'last_location_updated' => true,
            ]);

        $this->assertDatabaseHas('locations', [
            'visitador_medico_id' => $visitador->id,
        ]);

        $this->assertDatabaseHas('last_locations', [
            'visitador_medico_id' => $visitador->id,
        ]);

        $this->getJson('/api/me/last-location')
            ->assertOk()
            ->assertJson([
                'visitador_medico_id' => $visitador->id,
                'latitude' => (string) $payload['latitude'],
                'longitude' => (string) $payload['longitude'],
            ]);
    }
}
