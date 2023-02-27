<?php

namespace App\PaymentModule\Models;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use App\Enums\RepeatUnit;
use App\PaymentModule\Factories\PaymentFactory;
use App\TransferModule\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\PaymentModule\Models\Payment
 *
 * @property int $id
 * @property int $account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $description
 * @property int $amount_converted
 * @property int $amount
 * @property Currency $currency
 * @property \Illuminate\Support\Carbon $date
 * @property int $hidden
 * @property \Illuminate\Support\Carbon|null $ends_on
 * @property string $group
 * @property int $auto_apply
 * @property string|null $applied_at
 * @property-read Account|null $account
 * @property-read Transfer|null $from_transfer
 * @property-read Transfer|null $to_transfer
 *
 * @method static \App\PaymentModule\Factories\PaymentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmountConverted($value)
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
        'account_id',
        'group',
        'description',
        'amount',
        'amount_converted',
        'currency',
        'date',
        'hidden',
        'ends_on',
        'repeat_unit',
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
        return PaymentFactory::new();
    }

    public function from_transfer(): HasOne
    {
        return $this->hasOne(Transfer::class, 'to_payment_id');
    }

    public function to_transfer(): HasOne
    {
        return $this->hasOne(Transfer::class, 'from_payment_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
