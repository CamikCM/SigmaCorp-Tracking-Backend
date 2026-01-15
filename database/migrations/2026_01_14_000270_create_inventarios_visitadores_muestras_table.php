<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios_visitadores_muestras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitador_medico_id')
                ->constrained('visitadores_medicos')
                ->cascadeOnDelete();

            $table->foreignId('muestra_medica_id')
                ->constrained('muestras_medicas')
                ->restrictOnDelete();

            $table->integer('cantidad')->default(0);
            $table->timestamps();

            $table->unique(['visitador_medico_id', 'muestra_medica_id']);
            $table->index('muestra_medica_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios_visitadores_muestras');
    }
};
