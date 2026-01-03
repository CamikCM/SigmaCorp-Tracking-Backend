<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('visitas')) {
            Schema::create('visitas', function (Blueprint $table) {
                $table->id();

                $table->foreignId('usuario_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->foreignId('cliente_id')
                    ->constrained('clientes')
                    ->restrictOnDelete();

                $table->foreignId('jornada_id')
                    ->constrained('jornadas')
                    ->cascadeOnDelete();

                $table->foreignId('ruta_id')
                    ->nullable()
                    ->constrained('rutas')
                    ->nullOnDelete();

                $table->timestamp('check_in')->nullable();
                $table->timestamp('check_out')->nullable();

                $table->decimal('latitud', 10, 7)->nullable();
                $table->decimal('longitud', 10, 7)->nullable();

                $table->text('notas')->nullable();

                $table->timestamps();

                $table->index(['usuario_id', 'jornada_id']);
                $table->index('cliente_id');
                $table->index('check_in');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
