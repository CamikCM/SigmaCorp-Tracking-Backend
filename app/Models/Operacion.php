<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operacion extends Model
{
    use HasFactory;

    protected $table = 'operaciones';

    protected $fillable = [
        'tipo',
        'estado',
        'fecha',
        'emite_persona_id',
        'recibe_visitador_medico_id',
        'sucursal_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function emitePersona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'emite_persona_id');
    }

    public function recibeVisitadorMedico(): BelongsTo
    {
        return $this->belongsTo(VisitadorMedico::class, 'recibe_visitador_medico_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OperacionItem::class, 'operacion_id');
    }
}
