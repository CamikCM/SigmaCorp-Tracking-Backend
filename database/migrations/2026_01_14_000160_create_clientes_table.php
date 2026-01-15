<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('persona_id')
                ->unique()
                ->constrained('persona')
                ->cascadeOnDelete();

            $table->string('codigo')->unique();
            $table->string('tipo_cliente'); // medico,farmacia,institucion,otro

            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('categorias_clientes')
                ->nullOnDelete();

            $table->foreignId('sucursal_id')
                ->nullable()
                ->constrained('sucursales')
                ->nullOnDelete();

            $table->foreignId('especialidad_id')
                ->nullable()
                ->constrained('especialidades_medicas')
                ->nullOnDelete();

            $table->boolean('activo')->default(true);

            // PostGIS opcional (si cliente tiene punto distinto al de persona)
            // $table->point('geom')->nullable();

            $table->timestamps();

            $table->index('tipo_cliente');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
