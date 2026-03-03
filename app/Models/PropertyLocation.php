<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyLocation extends Model
{
    protected $fillable = [
        'property_id',
        'endereco_completo',
        'cidade',
        'estado',
        'cep',
        'bairro',
        'latitude',
        'longitude',
        'referencia',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
