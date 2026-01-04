<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inventarios_visitadores_muestras')) {
            Schema::create('inventarios_visitadores_muestras', function (Blueprint $table) {
                $table->id();

                $table->foreignId('usuario_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->foreignId('muestra_medica_id')
                    ->constrained('muestras_medicas')
                    ->restrictOnDelete();

                $table->integer('cantidad')->default(0);
                $table->timestamps();

                $table->unique(['usuario_id', 'muestra_medica_id'], 'inv_visitador_muestra_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios_visitadores_muestras');
    }
};
