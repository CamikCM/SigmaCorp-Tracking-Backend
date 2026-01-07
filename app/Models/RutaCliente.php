<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaCliente extends Model
{
    use HasFactory;

    protected $table = 'ruta_clientes';

    protected $fillable = [
        'ruta_id',
        'cliente_id',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
