<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'apellidos',
        'usuario',      // login opcional
        'email',
        'password',
        'device',
        'sucursal_id',
        'rol',
        'estado',       // OFF/ON
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relación con sucursal (si ya creaste la tabla sucursales)
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    // Un usuario tiene muchas ubicaciones (histórico)
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    // Última ubicación (una sola fila)
    public function lastLocation(): HasOne
    {
        return $this->hasOne(LastLocation::class);
    }

    // Jornadas del usuario (nuevo)
    public function jornadas(): HasMany
    {
        return $this->hasMany(Jornada::class, 'usuario_id');
    }

    // Visitas del usuario (nuevo)
    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class, 'usuario_id');
    }
}
