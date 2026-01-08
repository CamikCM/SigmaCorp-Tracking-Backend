<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'sucursal_id')) {
            return;
        }

        // Postgres: si ya existe la FK, la quitamos primero (evita "Duplicate object")
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_sucursal_id_foreign');

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sucursal_id', 'users_sucursal_id_foreign')
                ->references('id')
                ->on('sucursales')
                ->nullOnDelete();
        });

        // Índice (si ya existe no rompe)
        try {
            DB::statement('CREATE INDEX IF NOT EXISTS users_sucursal_id_index ON users (sucursal_id)');
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        // Quitar FK e índice de forma segura
        try {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_sucursal_id_foreign');
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            DB::statement('DROP INDEX IF EXISTS users_sucursal_id_index');
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
