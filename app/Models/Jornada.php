<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jornada extends Model
{
    use HasFactory;

    protected $table = 'jornadas';

    protected $fillable = [
        'usuario_id',
        'fecha',
        'hora_inicio_plan',
        'hora_inicio_almuerzo_plan',
        'hora_fin_almuerzo_plan',
        'hora_fin_plan',
        'inicio_real',
        'inicio_almuerzo_real',
        'fin_almuerzo_real',
        'fin_real',
        'estado',
        'tracking_habilitado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'inicio_real' => 'datetime',
        'inicio_almuerzo_real' => 'datetime',
        'fin_almuerzo_real' => 'datetime',
        'fin_real' => 'datetime',
        'tracking_habilitado' => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function visitas(): HasMany
    {
        // Evita error si Visita aún no existe
        return $this->hasMany('App\\Models\\Visita', 'jornada_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'jornada_id');
    }
}
