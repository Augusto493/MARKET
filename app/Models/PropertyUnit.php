<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyUnit extends Model
{
    protected $fillable = [
        'property_id',
        'stays_unit_id',
        'nome',
        'stays_raw_data',
    ];

    protected $casts = [
        'stays_raw_data' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
