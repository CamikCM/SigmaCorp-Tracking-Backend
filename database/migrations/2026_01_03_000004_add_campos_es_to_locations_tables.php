<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void
    {
        // Tabla locations (existente)
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'jornada_id')) {
                $table->foreignId('jornada_id')
                    ->nullable()
                    ->constrained('jornadas')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('locations', 'precision')) {
                $table->float('precision')->nullable();
            }
            if (!Schema::hasColumn('locations', 'velocidad')) {
                $table->float('velocidad')->nullable();
            }
            if (!Schema::hasColumn('locations', 'registrado_en')) {
                $table->timestamp('registrado_en')->nullable();
            }
        });

        // Backfill: registrado_en = created_at (no rompe el tracking actual)
        DB::table('locations')->whereNull('registrado_en')->update([
            'registrado_en' => DB::raw('created_at')
        ]);

        // Tabla last_locations (existente)
        Schema::table('last_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('last_locations', 'precision')) {
                $table->float('precision')->nullable();
            }
            if (!Schema::hasColumn('last_locations', 'velocidad')) {
                $table->float('velocidad')->nullable();
            }
            if (!Schema::hasColumn('last_locations', 'registrado_en')) {
                $table->timestamp('registrado_en')->nullable();
            }
        });

        DB::table('last_locations')->whereNull('registrado_en')->update([
            'registrado_en' => DB::raw('created_at')
        ]);

        DB::statement("CREATE INDEX IF NOT EXISTS locations_user_created_idx ON locations(user_id, created_at)");
        DB::statement("CREATE INDEX IF NOT EXISTS locations_jornada_registrado_idx ON locations(jornada_id, registrado_en)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS locations_user_created_idx");
        DB::statement("DROP INDEX IF EXISTS locations_jornada_registrado_idx");

        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'jornada_id')) {
                $table->dropConstrainedForeignId('jornada_id');
            }
            foreach (['registrado_en', 'velocidad', 'precision'] as $col) {
                if (Schema::hasColumn('locations', $col)) $table->dropColumn($col);
            }
        });

        Schema::table('last_locations', function (Blueprint $table) {
            foreach (['registrado_en', 'velocidad', 'precision'] as $col) {
                if (Schema::hasColumn('last_locations', $col)) $table->dropColumn($col);
            }
        });
    }
};
