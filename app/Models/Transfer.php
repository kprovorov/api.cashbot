<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transfer
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $from_payment_id
 * @property int $to_payment_id
 * @property-read \App\Models\Payment|null $payment_from
 * @property-read \App\Models\Payment|null $payment_to
 *
 * @method static \Database\Factories\TransferFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereFromPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereToPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
