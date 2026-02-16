<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'user_id',
        'flight_id',
        'flight_class_id',
        'name',
        'email',
        'phone',
        'number_of_passengers',
        'promo_code_id',
        'payment_status',
        'payment_method',
        'subtotal',
        'grandtotal',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function flightClass()
    {
        return $this->belongsTo(FlightClass::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function passengers()
    {
        return $this->hasMany(TransactionPassenger::class);
    }
}
