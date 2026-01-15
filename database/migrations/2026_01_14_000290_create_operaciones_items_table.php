<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operaciones_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('operacion_id')
                ->constrained('operaciones')
                ->cascadeOnDelete();

            $table->foreignId('muestra_medica_id')
                ->constrained('muestras_medicas')
                ->restrictOnDelete();

            $table->integer('cantidad');
            $table->timestamps();

            $table->unique(['operacion_id', 'muestra_medica_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operaciones_items');
    }
};
