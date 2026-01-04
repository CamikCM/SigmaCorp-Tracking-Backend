<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inventarios_sucursales_muestras')) {
            Schema::create('inventarios_sucursales_muestras', function (Blueprint $table) {
                $table->id();

                $table->foreignId('sucursal_id')
                    ->constrained('sucursales')
                    ->cascadeOnDelete();

                $table->foreignId('muestra_medica_id')
                    ->constrained('muestras_medicas')
                    ->restrictOnDelete();

                $table->integer('cantidad')->default(0);
                $table->timestamps();

                $table->unique(['sucursal_id', 'muestra_medica_id'], 'inv_sucursal_muestra_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios_sucursales_muestras');
    }
};
