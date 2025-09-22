<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservas extends Model
{
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'data',
        'hora'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function restaurant()
    {
        return $this->belongsTo(User::class, "restaurant_id");
    }
}
