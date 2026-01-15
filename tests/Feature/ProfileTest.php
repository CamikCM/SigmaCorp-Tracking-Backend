<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_skipped(): void
    {
        $this->markTestSkipped('Este proyecto no usa el perfil web por defecto (Breeze).');
    }
}
