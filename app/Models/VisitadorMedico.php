<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisitadorMedico extends Model
{
    use HasFactory;

    protected $table = 'visitadores_medicos';

    protected $fillable = [
        'persona_id',
        'sucursal_id',
        'estado_user_id',
        'codigo',
        'activo',
        'last_ping_at',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'last_ping_at' => 'datetime',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function estadoUser(): BelongsTo
    {
        return $this->belongsTo(EstadoUser::class, 'estado_user_id');
    }

    public function jornadas(): HasMany
    {
        return $this->hasMany(Jornada::class, 'visitador_medico_id');
    }

    public function eventosTracking(): HasMany
    {
        return $this->hasMany(VisitadorEstadoTracking::class, 'visitador_medico_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'visitador_medico_id');
    }

    public function lastLocation(): HasOne
    {
        return $this->hasOne(LastLocation::class, 'visitador_medico_id');
    }

    public function asignacionesRutas(): HasMany
    {
        return $this->hasMany(AsignacionRuta::class, 'visitador_medico_id');
    }

    public function rutas(): BelongsToMany
    {
        return $this->belongsToMany(Ruta::class, 'asignaciones_rutas', 'visitador_medico_id', 'ruta_id')
            ->withPivot(['activo'])
            ->withTimestamps();
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class, 'visitador_medico_id');
    }

    public function inventariosMuestras(): HasMany
    {
        return $this->hasMany(InventarioVisitadorMuestra::class, 'visitador_medico_id');
    }

    public function operacionesRecibidas(): HasMany
    {
        return $this->hasMany(Operacion::class, 'recibe_visitador_medico_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
