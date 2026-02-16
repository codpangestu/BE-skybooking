<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image',
        'name',
        'description',
    ];

    public function classes()
    {
        return $this->belongsToMany(FlightClass::class, 'flight_class_facility', 'facility_id', 'flight_class_id');
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
