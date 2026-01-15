<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Única FK de negocio permitida en users:
            $table->foreignId('persona_id')
                ->unique()
                ->constrained('persona')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('device')->nullable();
            $table->rememberToken();

            // Auditoría mínima / control de acceso
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
