<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('apellido_pat')->nullable();
            $table->string('apellido_mat')->nullable();
            $table->string('telefono_principal')->nullable();
            $table->string('telefono_secundario')->nullable();
            $table->string('email_personal')->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('habilitado')->default(true);
            $table->string('carnet_identidad')->nullable();
            $table->string('foto_url')->nullable();

            // PostGIS opcional (activar luego)
            // $table->point('geom')->nullable(); // o geography(Point,4326) vía raw SQL

            $table->timestamps();
            $table->softDeletes();

            $table->index('carnet_identidad');
            $table->index('email_personal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
