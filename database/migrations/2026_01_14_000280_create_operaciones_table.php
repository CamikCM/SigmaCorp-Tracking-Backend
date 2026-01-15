<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operaciones', function (Blueprint $table) {
            $table->id();

            $table->string('tipo');   // asignacion, devolucion, ajuste, transferencia
            $table->string('estado'); // pendiente, confirmado, rechazado
            $table->timestamp('fecha');

            // Emisor (auditoría): persona (supervisor o informatica)
            $table->foreignId('emite_persona_id')
                ->constrained('persona')
                ->restrictOnDelete();

            // Receptor: visitador (opcional)
            $table->foreignId('recibe_visitador_medico_id')
                ->nullable()
                ->constrained('visitadores_medicos')
                ->nullOnDelete();

            // Sucursal involucrada (opcional)
            $table->foreignId('sucursal_id')
                ->nullable()
                ->constrained('sucursales')
                ->nullOnDelete();

            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('tipo');
            $table->index('estado');
            $table->index('fecha');
            $table->index('emite_persona_id');
            $table->index('recibe_visitador_medico_id');
            $table->index('sucursal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operaciones');
    }
};
