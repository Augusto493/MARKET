<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAmenity extends Model
{
    protected $fillable = [
        'property_id',
        'nome',
        'icone',
        'categoria',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
