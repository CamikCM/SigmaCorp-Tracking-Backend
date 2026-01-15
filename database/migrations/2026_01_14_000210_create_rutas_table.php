<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sucursal_id')
                ->constrained('sucursales')
                ->restrictOnDelete();

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->index('sucursal_id');
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
