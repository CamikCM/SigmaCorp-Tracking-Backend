<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'apellidos')) {
                $table->string('apellidos')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'usuario')) {
                $table->string('usuario')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'sucursal_id')) {
                // FK se agrega en la siguiente migración (add_fk_users_sucursal_id)
                $table->unsignedBigInteger('sucursal_id')->nullable()->after('device');
            }

            if (Schema::hasColumn('users', 'rol')) {
                // compatibilidad fase 1: mantener rol pero default
                $table->string('rol', 50)->default('visitador')->change();
            }

            if (Schema::hasColumn('users', 'estado')) {
                $table->string('estado', 10)->default('OFF')->change();
            } else {
                $table->string('estado', 10)->default('OFF')->after('rol');
            }
        });

        // Backfill: asegurar OFF si estaba null
        DB::table('users')->whereNull('estado')->update(['estado' => 'OFF']);

        // Índice unique parcial para usuario (solo cuando usuario no es null)
        try {
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS users_usuario_unique_not_null ON users (usuario) WHERE usuario IS NOT NULL");
        } catch (\Throwable $e) {
            // Ignorar si DB no soporta (ej: sqlite)
        }

        // CHECK constraint estado OFF/ON (Postgres)
        try {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_estado_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_estado_check CHECK (estado IN ('OFF','ON'))");
        } catch (\Throwable $e) {
            // Ignorar si DB no soporta
        }
    }

    public function down(): void
    {
        // Quitar constraint e índice parcial
        try { DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_estado_check"); } catch (\Throwable $e) {}
        try { DB::statement("DROP INDEX IF EXISTS users_usuario_unique_not_null"); } catch (\Throwable $e) {}

        Schema::table('users', function (Blueprint $table) {
            // OJO: no borro sucursal_id aquí porque puede tener datos.
            // Si quieres revertir completo, descomenta:
            // if (Schema::hasColumn('users', 'sucursal_id')) { $table->dropColumn('sucursal_id'); }

            if (Schema::hasColumn('users', 'usuario')) {
                $table->dropColumn('usuario');
            }

            if (Schema::hasColumn('users', 'apellidos')) {
                $table->dropColumn('apellidos');
            }

            // estado lo dejamos (tu sistema ya lo usa)
        });
    }
};
