<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarkupRule extends Model
{
    protected $fillable = [
        'nome',
        'tipo',
        'owner_id',
        'property_id',
        'markup_type',
        'markup_value',
        'data_inicio',
        'data_fim',
        'dias_semana',
        'min_noites',
        'max_noites',
        'ativo',
        'prioridade',
    ];

    protected $casts = [
        'markup_value' => 'decimal:2',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'dias_semana' => 'array',
        'ativo' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeActive($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeByType($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
