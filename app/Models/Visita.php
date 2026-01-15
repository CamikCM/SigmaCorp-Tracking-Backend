<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';

    protected $fillable = [
        'visitador_medico_id',
        'cliente_id',
        'jornada_id',
        'fecha',
        'latitud',
        'longitud',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    public function visitadorMedico(): BelongsTo
    {
        return $this->belongsTo(VisitadorMedico::class, 'visitador_medico_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function jornada(): BelongsTo
    {
        return $this->belongsTo(Jornada::class, 'jornada_id');
    }
}
