<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supervisor extends Model
{
    use HasFactory;

    protected $table = 'supervisores';

    protected $fillable = [
        'persona_id',
        'sucursal_id',
        'cargo',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
