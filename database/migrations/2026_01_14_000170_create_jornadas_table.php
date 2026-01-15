<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jornadas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitador_medico_id')
                ->constrained('visitadores_medicos')
                ->restrictOnDelete();

            $table->date('fecha');

            $table->timestamp('inicio_jornada')->nullable();
            $table->timestamp('fin_jornada')->nullable();

            $table->timestamp('inicio_almuerzo')->nullable();
            $table->timestamp('fin_almuerzo')->nullable();

            $table->string('estado')->default('abierta'); // abierta, cerrada

            $table->timestamps();

            $table->unique(['visitador_medico_id', 'fecha']);
            $table->index(['visitador_medico_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jornadas');
    }
};
