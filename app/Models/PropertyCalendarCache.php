<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyCalendarCache extends Model
{
    protected $table = 'property_calendar_cache';

    protected $fillable = [
        'property_id',
        'data',
        'status',
        'min_nights',
        'max_nights',
        'cached_at',
    ];

    protected $casts = [
        'data' => 'date',
        'cached_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
