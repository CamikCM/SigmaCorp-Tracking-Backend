<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitadores_medicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('persona_id')
                ->unique()
                ->constrained('persona')
                ->cascadeOnDelete();

            $table->foreignId('sucursal_id')
                ->constrained('sucursales')
                ->restrictOnDelete();

            $table->foreignId('estado_user_id')
                ->constrained('estado_user')
                ->restrictOnDelete();

            $table->string('codigo')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('last_ping_at')->nullable();

            $table->timestamps();

            $table->index(['sucursal_id', 'estado_user_id']);
            $table->index('activo');
            $table->index('last_ping_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitadores_medicos');
    }
};
