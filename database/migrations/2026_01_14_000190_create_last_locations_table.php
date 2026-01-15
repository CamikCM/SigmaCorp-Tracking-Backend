<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('last_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitador_medico_id')
                ->unique()
                ->constrained('visitadores_medicos')
                ->cascadeOnDelete();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('recorded_at');

            // PostGIS opcional
            // $table->point('geom')->nullable();

            $table->timestamps();

            $table->index(['visitador_medico_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('last_locations');
    }
};
