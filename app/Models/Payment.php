<?php

namespace App\Models;

use App\Enums\Currency;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $jar_id
 * @property string $description
 * @property int $amount
 * @property int $original_amount
 * @property Currency $currency
 * @property \Illuminate\Support\Carbon $date
 * @property int|null $group_id
 * @property int $hidden
 * @property \Illuminate\Support\Carbon|null $ends_on
 * @property-read \App\Models\Transfer|null $from_transfer
 * @property-read \App\Models\Group|null $group
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
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEndsOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereJarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereOriginalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'jar_id',
        'description',
        'amount',
        'original_amount',
        'currency',
        'date',
        'hidden',
    ];

    protected $casts = [
        'balance'             => 'integer',
        'jar_balance'         => 'integer',
        'jar_savings_balance' => 'integer',
        'currency'            => Currency::class,
        'date'                => 'date',
        'ends_on'             => 'date',
    ];

    /**
     * @return HasOne
     */
    public function from_transfer(): HasOne
    {
        return $this->hasOne(Transfer::class, 'to_payment_id');
    }

    /**
     * @return HasOne
     */
    public function to_transfer(): HasOne
    {
        return $this->hasOne(Transfer::class, 'from_payment_id');
    }

    /**
     * @return BelongsTo
     */
    public function jar(): BelongsTo
    {
        return $this->belongsTo(Jar::class);
    }

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
