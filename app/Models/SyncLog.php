<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'owner_id',
        'tipo',
        'status',
        'properties_synced',
        'properties_created',
        'properties_updated',
        'properties_failed',
        'error_message',
        'details',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'details' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
