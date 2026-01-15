<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitador_medico_id')
                ->constrained('visitadores_medicos')
                ->cascadeOnDelete();

            $table->foreignId('jornada_id')
                ->nullable()
                ->constrained('jornadas')
                ->nullOnDelete();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('accuracy', 10, 2)->nullable();
            $table->decimal('speed', 10, 2)->nullable();
            $table->decimal('heading', 10, 2)->nullable();
            $table->decimal('altitude', 10, 2)->nullable();
            $table->string('provider')->nullable();
            $table->timestamp('recorded_at');

            // PostGIS opcional
            // $table->point('geom')->nullable();

            $table->timestamps();

            $table->index(['visitador_medico_id', 'recorded_at']);
            $table->index(['jornada_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
