<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta_clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ruta_id')
                ->constrained('rutas')
                ->cascadeOnDelete();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->integer('orden')->nullable();
            $table->timestamps();

            $table->unique(['ruta_id', 'cliente_id']);
            $table->index(['ruta_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_clientes');
    }
};
