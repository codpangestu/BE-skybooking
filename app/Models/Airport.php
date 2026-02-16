<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'iata_code',
        'name',
        'image',
        'city',
        'country'
    ];

    public function segments()
    {
        return $this->hasMany(FlightSegment::class);
    }

    public function getImageAttribute($value)
    {
        if (!$value)
            return null;
        if (strpos($value, 'http') === 0)
            return $value;

        if (request()->is('api/*')) {
            return asset('storage/' . $value);
        }

        return $value;
    }
}
