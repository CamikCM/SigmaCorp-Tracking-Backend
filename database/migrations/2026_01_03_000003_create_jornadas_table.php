<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('jornadas')) {
            Schema::create('jornadas', function (Blueprint $table) {
                $table->id();

                $table->foreignId('usuario_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->date('fecha');

                // Horario planificado (snapshot del día)
                $table->time('hora_inicio_plan')->default('08:00:00');
                $table->time('hora_inicio_almuerzo_plan')->default('12:00:00');
                $table->time('hora_fin_almuerzo_plan')->default('14:00:00');
                $table->time('hora_fin_plan')->default('18:00:00');

                // Marcadores reales
                $table->timestamp('inicio_real')->nullable();
                $table->timestamp('inicio_almuerzo_real')->nullable();
                $table->timestamp('fin_almuerzo_real')->nullable();
                $table->timestamp('fin_real')->nullable();

                $table->string('estado', 20)->default('pendiente'); // pendiente|activa|pausada|finalizada
                $table->boolean('tracking_habilitado')->default(false);

                $table->timestamps();

                $table->unique(['usuario_id', 'fecha'], 'jornadas_usuario_fecha_unique');
                $table->index('estado');
                $table->index('tracking_habilitado');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jornadas');
    }
};
