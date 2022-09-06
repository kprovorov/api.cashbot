<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $jar_id
 * @property string $description
 * @property int $amount
 * @property string $currency
 * @property string $date
 * @property-read \App\Models\Transfer|null $from_transfer
 * @property-read \App\Models\Jar|null $jar
 * @property-read \App\Models\Transfer|null $to_transfer
 * @method static \Database\Factories\PaymentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereJarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'jar_id',
        'description',
        'amount',
        'currency',
        'date',
    ];

    protected $casts = [
        'balance'             => 'integer',
        'jar_balance'         => 'integer',
        'jar_savings_balance' => 'integer',
    ];

    public function from_transfer()
    {
        return $this->hasOne(Transfer::class, 'to_payment_id');
    }

    public function to_transfer()
    {
        return $this->hasOne(Transfer::class, 'from_payment_id');
    }

    public function jar()
    {
        return $this->belongsTo(Jar::class);
    }
}
