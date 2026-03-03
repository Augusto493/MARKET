<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPhoto extends Model
{
    protected $fillable = [
        'property_id',
        'url',
        'thumbnail_url',
        'stays_photo_id',
        'hash',
        'ordem',
        'principal',
        'legenda',
    ];

    protected $casts = [
        'principal' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
