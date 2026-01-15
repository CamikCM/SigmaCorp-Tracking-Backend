<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    public function test_skipped(): void
    {
        $this->markTestSkipped('Este proyecto usa autenticación por API / Filament, no el flujo web por defecto.');
    }
}
