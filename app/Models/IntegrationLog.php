<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationLog extends Model
{
    protected $fillable = [
        'owner_id',
        'property_id',
        'reservation_id',
        'tipo',
        'status',
        'endpoint',
        'method',
        'status_code',
        'request_body',
        'response_body',
        'error_message',
        'duration_ms',
    ];

    protected $casts = [
        'duration_ms' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
