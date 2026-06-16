<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = ['issued_at' => 'datetime', 'subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
