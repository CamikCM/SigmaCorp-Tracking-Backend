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
        'fecha_registro',
        'tipo',
        'comprobante',
        'sucursal_id',
        'entrega_usuario_id',
        'recibe_usuario_id',
        'total_unidades',
        'observacion',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'total_unidades' => 'integer',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function entregaUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entrega_usuario_id');
    }

    public function recibeUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recibe_usuario_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OperacionItem::class, 'operacion_id');
    }
}
