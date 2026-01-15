<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitador_medico_id')
                ->constrained('visitadores_medicos')
                ->restrictOnDelete();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->restrictOnDelete();

            $table->foreignId('jornada_id')
                ->nullable()
                ->constrained('jornadas')
                ->nullOnDelete();

            $table->timestamp('fecha');

            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index(['visitador_medico_id', 'fecha']);
            $table->index('cliente_id');
            $table->index('jornada_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
