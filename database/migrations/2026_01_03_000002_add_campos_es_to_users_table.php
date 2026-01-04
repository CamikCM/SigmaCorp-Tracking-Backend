<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void
    {
        // Agrega columnas sin romper lo existente
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'apellidos')) {
                $table->string('apellidos')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'usuario')) {
                $table->string('usuario')->nullable()->after('email'); // login opcional
            }
            if (!Schema::hasColumn('users', 'sucursal_id')) {
                $table->foreignId('sucursal_id')
                    ->nullable()
                    ->constrained('sucursales')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'rol')) {
                $table->string('rol', 50)->default('visitador');
            }
            if (!Schema::hasColumn('users', 'estado')) {
                // OFF / ON según tu regla
                $table->string('estado', 10)->default('OFF');
            }
        });

        // Índices útiles
        DB::statement("CREATE INDEX IF NOT EXISTS users_sucursal_id_index ON users(sucursal_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS users_estado_index ON users(estado)");
        DB::statement("CREATE INDEX IF NOT EXISTS users_rol_index ON users(rol)");

        // Unique parcial para usuario (permite múltiples NULL en Postgres)
        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS users_usuario_unique ON users(usuario) WHERE usuario IS NOT NULL");

        // Backfill (por si existieran nulos)
        DB::table('users')->whereNull('estado')->update(['estado' => 'OFF']);
        DB::table('users')->whereNull('rol')->update(['rol' => 'visitador']);
    }

    public function down(): void
    {
        // Ojo: bajar esto puede fallar si ya hay dependencias creadas por otros cambios.
        DB::statement("DROP INDEX IF EXISTS users_usuario_unique");
        DB::statement("DROP INDEX IF EXISTS users_sucursal_id_index");
        DB::statement("DROP INDEX IF EXISTS users_estado_index");
        DB::statement("DROP INDEX IF EXISTS users_rol_index");

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sucursal_id')) {
                $table->dropConstrainedForeignId('sucursal_id');
            }
            if (Schema::hasColumn('users', 'estado')) $table->dropColumn('estado');
            if (Schema::hasColumn('users', 'rol')) $table->dropColumn('rol');
            if (Schema::hasColumn('users', 'usuario')) $table->dropColumn('usuario');
            if (Schema::hasColumn('users', 'apellidos')) $table->dropColumn('apellidos');
        });
    }
};
