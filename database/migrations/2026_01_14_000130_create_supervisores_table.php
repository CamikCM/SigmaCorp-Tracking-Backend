<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('persona_id')
                ->unique()
                ->constrained('persona')
                ->cascadeOnDelete();

            $table->foreignId('sucursal_id')
                ->constrained('sucursales')
                ->restrictOnDelete();

            $table->string('cargo')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index('sucursal_id');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisores');
    }
};
