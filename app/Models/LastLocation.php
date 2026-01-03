<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LastLocation extends Model
{
    use HasFactory;

    protected $table = 'last_locations';

    protected $fillable = ['user_id','latitude','longitude'];
    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
