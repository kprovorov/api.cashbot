<?php

namespace App\TransferModule\Models;

use App\PaymentModule\Models\Payment;
use App\TransferModule\Factories\TransferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\TransferModule\Models\Transfer
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $from_payment_id
 * @property int $to_payment_id
 * @property-read Payment|null $payment_from
 * @property-read Payment|null $payment_to
 * @method static \App\TransferModule\Factories\TransferFactory factory(...$parameters)
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_payment_id',
        'to_payment_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return TransferFactory
     */
    protected static function newFactory(): TransferFactory
    {
        return TransferFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function payment_to(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'to_payment_id');
    }

    /**
     * @return BelongsTo
     */
    public function payment_from(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'from_payment_id');
    }
}
