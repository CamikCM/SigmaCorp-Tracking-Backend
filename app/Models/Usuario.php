<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $table = 'users';
 #   protected string $guard_name = 'web';

    protected $fillable = [
        'persona_id',
        'name',
        'email',
        'password',
        'device',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Filament: control básico de acceso. Mantén la lógica fina en policies/permisos.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Bloqueo global (si lo usas)
        if (! $this->is_active) {
            return false;
        }

        // Super admin SIEMPRE entra a cualquier panel
        $superAdminRole = config('filament-shield.super_admin.name', 'super_admin');
        if ($this->hasRole($superAdminRole)) {
            return true;
        }

        // ✅ IMPORTANTÍSIMO: deben coincidir con tus custom_permissions de Shield
        if ($panel->getId() === 'informatica') {
            return $this->hasPermissionTo('access_informatica_panel');
        }

        if ($panel->getId() === 'supervisor') {
            return $this->hasPermissionTo('access_supervisor_panel');
        }

        // Si agregas más paneles en el futuro, aquí controlas el acceso
        return false;
    }
}
