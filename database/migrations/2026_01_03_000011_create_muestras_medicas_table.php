<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('muestras_medicas')) {
            Schema::create('muestras_medicas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('tipo')->nullable();
                $table->text('descripcion')->nullable();
                $table->boolean('activa')->default(true);
                $table->timestamps();

                $table->index('activa');
                $table->index('nombre');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('muestras_medicas');
    }
};
