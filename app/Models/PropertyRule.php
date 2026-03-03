<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyRule extends Model
{
    protected $fillable = [
        'property_id',
        'tipo',
        'valor',
        'descricao',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
