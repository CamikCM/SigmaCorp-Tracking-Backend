<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones_rutas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ruta_id')
                ->constrained('rutas')
                ->cascadeOnDelete();

            $table->foreignId('visitador_medico_id')
                ->constrained('visitadores_medicos')
                ->cascadeOnDelete();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(['ruta_id', 'visitador_medico_id']);
            $table->index(['visitador_medico_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_rutas');
    }
};
