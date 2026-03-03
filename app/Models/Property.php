<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'stays_property_id',
        'stays_unit_id',
        'nome',
        'descricao',
        'descricao_curta',
        'capacidade_hospedes',
        'quartos',
        'camas',
        'banheiros',
        'cidade',
        'bairro',
        'latitude',
        'longitude',
        'ativo',
        'publicado_marketplace',
        'prioridade',
        'destaque',
        'titulo_marketing',
        'tags',
        'stays_raw_data',
        'stays_synced_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'stays_raw_data' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ativo' => 'boolean',
        'publicado_marketplace' => 'boolean',
        'destaque' => 'boolean',
        'stays_synced_at' => 'datetime',
    ];

    // Relações
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('ordem')->orderBy('id');
    }

    public function amenities()
    {
        return $this->hasMany(PropertyAmenity::class);
    }

    public function rules()
    {
        return $this->hasMany(PropertyRule::class);
    }

    public function location()
    {
        return $this->hasOne(PropertyLocation::class);
    }

    public function units()
    {
        return $this->hasMany(PropertyUnit::class);
    }

    public function calendarCache()
    {
        return $this->hasMany(PropertyCalendarCache::class);
    }

    public function rateCache()
    {
        return $this->hasMany(PropertyRateCache::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function markupRules()
    {
        return $this->hasMany(MarkupRule::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePublished($query)
    {
        return $query->where('publicado_marketplace', true)->where('ativo', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('destaque', true);
    }

    // Accessors
    public function getMainPhotoAttribute()
    {
        return $this->photos()->where('principal', true)->first() 
            ?? $this->photos()->first();
    }
}
