<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('clientes')) {
            Schema::create('clientes', function (Blueprint $table) {
                $table->id();

                $table->string('codigo', 50)->nullable(); // AA|AAA|A|B
                $table->string('abreviatura', 100)->nullable();
                $table->string('nombre');
                $table->string('especialidad')->nullable();
                $table->text('descripcion')->nullable();

                $table->string('direccion')->nullable();
                $table->decimal('latitud', 10, 7)->nullable();
                $table->decimal('longitud', 10, 7)->nullable();

                $table->foreignId('categoria_id')
                    ->nullable()
                    ->constrained('categorias_clientes')
                    ->nullOnDelete();

                $table->boolean('activo')->default(true);

                $table->timestamps();

                $table->index('categoria_id');
                $table->index('codigo');
                $table->index('activo');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
