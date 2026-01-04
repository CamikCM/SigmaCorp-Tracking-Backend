<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('ruta_clientes')) {
            Schema::create('ruta_clientes', function (Blueprint $table) {
                $table->id();

                $table->foreignId('ruta_id')
                    ->constrained('rutas')
                    ->cascadeOnDelete();

                $table->foreignId('cliente_id')
                    ->constrained('clientes')
                    ->cascadeOnDelete();

                $table->unsignedInteger('orden')->default(1);

                $table->timestamps();

                $table->unique(['ruta_id', 'cliente_id'], 'ruta_clientes_unique');
                $table->index(['ruta_id', 'orden']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_clientes');
    }
};
