<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('operaciones')) {
            Schema::create('operaciones', function (Blueprint $table) {
                $table->id();

                $table->timestamp('fecha_registro'); // fecha de registro
                $table->string('tipo', 50); // entrega|devolucion|ajuste
                $table->string('comprobante')->nullable();

                $table->foreignId('sucursal_id')
                    ->constrained('sucursales')
                    ->restrictOnDelete();

                $table->foreignId('entrega_usuario_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('recibe_usuario_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->integer('total_unidades')->default(0);
                $table->text('observacion')->nullable();

                $table->timestamps();

                $table->index(['sucursal_id', 'fecha_registro']);
                $table->index(['tipo', 'fecha_registro']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('operaciones');
    }
};
