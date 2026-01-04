<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('rutas')) {
            Schema::create('rutas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->text('descripcion')->nullable();

                $table->foreignId('sucursal_id')
                    ->constrained('sucursales')
                    ->restrictOnDelete();

                $table->boolean('activa')->default(true);

                $table->timestamps();

                $table->index('sucursal_id');
                $table->index('activa');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
