<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios_sucursales_muestras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sucursal_id')
                ->constrained('sucursales')
                ->restrictOnDelete();

            $table->foreignId('muestra_medica_id')
                ->constrained('muestras_medicas')
                ->restrictOnDelete();

            $table->integer('cantidad')->default(0);
            $table->timestamps();

            $table->unique(['sucursal_id', 'muestra_medica_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios_sucursales_muestras');
    }
};
