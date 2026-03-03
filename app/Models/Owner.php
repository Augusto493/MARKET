<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Owner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nome',
        'email',
        'telefone',
        'status',
        'stays_base_url',
        'stays_client_id',
        'stays_client_secret',
        'stays_token',
        'stays_account_identifier',
        'webhook_secret',
        'sync_status',
        'last_sync_at',
        'last_sync_error',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
    ];

    // Relações
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(SyncLog::class);
    }

    public function markupRules()
    {
        return $this->hasMany(MarkupRule::class);
    }

    // Accessors/Mutators para credenciais criptografadas
    public function setStaysClientSecretAttribute($value)
    {
        $this->attributes['stays_client_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getStaysClientSecretAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setStaysTokenAttribute($value)
    {
        $this->attributes['stays_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getStaysTokenAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
