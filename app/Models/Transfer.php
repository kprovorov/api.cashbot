<?php

namespace App\Models;

use App\PaymentModule\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_payment_id',
        'to_payment_id',
    ];

    public function payment_to()
    {
        return $this->belongsTo(Payment::class, 'to_payment_id');
    }

    public function payment_from()
    {
        return $this->belongsTo(Payment::class, 'from_payment_id');
    }
}
