<?php

namespace App\PaymentModule\Models;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use App\Enums\RepeatUnit;
use App\PaymentModule\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\PaymentModule\Models\Payment
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $account_to_id
 * @property int|null $account_from_id
 * @property string $description
 * @property int|null $amount_to_converted
 * @property int|null $amount_from_converted
 * @property int $amount
 * @property Currency $currency
 * @property \Illuminate\Support\Carbon $date
 * @property int $hidden
 * @property \Illuminate\Support\Carbon|null $ends_on
 * @property string $group
 * @property int $auto_apply
 * @property string|null $applied_at
 * @property RepeatUnit $repeat_unit
 * @property int $repeat_interval
 * @property \Illuminate\Support\Carbon|null $repeat_ends_on
 * @property-read Account|null $accountFrom
 * @property-read Account|null $accountTo
 * @method static \App\PaymentModule\Factories\PaymentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAccountFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAccountToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmountFromConverted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmountToConverted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAppliedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAutoApply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEndsOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRepeatEndsOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRepeatInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRepeatUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'account_to_id',
        'account_from_id',
        'description',
        'amount_to_converted',
        'amount_from_converted',
        'amount',
        'currency',
        'date',
        'hidden',
        'ends_on',
        'group',
        'auto_apply',
        'applied_at',
        'repeat_unit',
        'repeat_interval',
        'repeat_ends_on',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'integer',
        'currency' => Currency::class,
        'date' => 'date',
        'ends_on' => 'date',
        'repeat_unit' => RepeatUnit::class,
        'repeat_ends_on' => 'date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        //
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new ();
    }

    public function account_to(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_to_id');
    }

    public function account_from(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_from_id');
    }
}
