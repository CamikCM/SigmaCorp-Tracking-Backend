<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('asignaciones_rutas')) {
            Schema::create('asignaciones_rutas', function (Blueprint $table) {
                $table->id();

                $table->foreignId('ruta_id')
                    ->constrained('rutas')
                    ->cascadeOnDelete();

                $table->foreignId('usuario_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->date('fecha_inicio');
                $table->date('fecha_fin')->nullable();
                $table->boolean('activa')->default(true);

                $table->timestamps();

                $table->unique(['ruta_id', 'usuario_id', 'fecha_inicio'], 'asig_ruta_usuario_inicio_unique');
                $table->index(['usuario_id', 'activa']);
                $table->index(['ruta_id', 'activa']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_rutas');
    }
};
