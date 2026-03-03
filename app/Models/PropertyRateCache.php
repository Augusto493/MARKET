<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyRateCache extends Model
{
    protected $table = 'property_rate_cache';

    protected $fillable = [
        'property_id',
        'data',
        'preco_base',
        'moeda',
        'taxa_limpeza',
        'cached_at',
    ];

    protected $casts = [
        'data' => 'date',
        'preco_base' => 'decimal:2',
        'taxa_limpeza' => 'decimal:2',
        'cached_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
