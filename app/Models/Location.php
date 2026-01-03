<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'precision',
        'velocidad',
        'registrado_en',
        'jornada_id',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'precision' => 'float',
        'velocidad' => 'float',
        'registrado_en' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación a jornada (nuevo)
    public function jornada(): BelongsTo
    {
        return $this->belongsTo(Jornada::class, 'jornada_id');
    }
}
