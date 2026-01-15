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
        'visitador_medico_id',
        'fecha',
        'inicio_jornada',
        'fin_jornada',
        'inicio_almuerzo',
        'fin_almuerzo',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'inicio_jornada' => 'datetime',
        'fin_jornada' => 'datetime',
        'inicio_almuerzo' => 'datetime',
        'fin_almuerzo' => 'datetime',
    ];

    public function visitadorMedico(): BelongsTo
    {
        return $this->belongsTo(VisitadorMedico::class, 'visitador_medico_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'jornada_id');
    }

    public function eventosTracking(): HasMany
    {
        return $this->hasMany(VisitadorEstadoTracking::class, 'jornada_id');
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class, 'jornada_id');
    }
}
