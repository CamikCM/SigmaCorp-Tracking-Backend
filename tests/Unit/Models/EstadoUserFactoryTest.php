<?php

namespace Tests\Unit\Models;

use App\Models\EstadoUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstadoUserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_estados_off_on(): void
    {
        $off = EstadoUser::factory()->off()->create();
        $on = EstadoUser::factory()->on()->create();

        $this->assertSame('OFF', $off->codigo);
        $this->assertSame('ON', $on->codigo);
    }
}
