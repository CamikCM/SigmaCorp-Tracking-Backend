<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitador_estado_tracking', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitador_medico_id')
                ->constrained('visitadores_medicos')
                ->restrictOnDelete();

            $table->foreignId('jornada_id')
                ->nullable()
                ->constrained('jornadas')
                ->nullOnDelete();

            $table->foreignId('estado_user_id')
                ->constrained('estado_user')
                ->restrictOnDelete();

            $table->string('tipo_marcado'); // inicio, pausa, reanudar, fin
            $table->string('fuente')->default('app'); // app, admin, sistema
            $table->timestamp('marcado_en');
            $table->string('nota')->nullable();

            $table->timestamps();

            $table->index(['visitador_medico_id', 'marcado_en']);
            $table->index(['jornada_id', 'marcado_en']);
            $table->index('tipo_marcado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitador_estado_tracking');
    }
};
