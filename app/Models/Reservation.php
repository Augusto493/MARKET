<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'codigo',
        'owner_id',
        'property_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guests_count',
        'checkin_date',
        'checkout_date',
        'nights',
        'base_total',
        'markup_total',
        'final_total',
        'cleaning_fee',
        'currency',
        'status',
        'stays_reservation_id',
        'error_message',
        'raw_payload_json',
        'origem',
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'checkout_date' => 'date',
        'base_total' => 'decimal:2',
        'markup_total' => 'decimal:2',
        'final_total' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'raw_payload_json' => 'array',
    ];

    // Relações
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function integrationLogs()
    {
        return $this->hasMany(IntegrationLog::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Reservation $reservation) {
            if (empty($reservation->codigo)) {
                $reservation->codigo = 'RES-' . strtoupper(Str::substr(str_replace('-', '', Str::uuid()->toString()), 0, 8));
            }
        });
    }

    // Scopes
    public function scopeByCodigo($query, string $codigo)
    {
        return $query->where('codigo', strtoupper($codigo));
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['lead', 'pending']);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
