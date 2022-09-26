<?php

namespace App\AccountModule\Models;

use App\AccountModule\Factories\AccountFactory;
use App\Enums\Currency;
use App\PaymentModule\Models\Payment;
use App\Services\CurrencyConverter;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * App\AccountModule\Models\Account
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property Currency $currency
 * @property int $balance
 * @property string|null $external_id
 * @property string|null $provider
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountModule\Models\Jar[] $jars
 * @property-read int|null $jars_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Payment[] $payments
 * @property-read int|null $payments_count
 * @method static \App\AccountModule\Factories\AccountFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'currency',
        'balance',
        'external_id',
        'provider',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    protected $casts = [
        'balance' => 'int',
        'uah_balance' => 'int',
        'currency' => Currency::class,
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return AccountFactory
     */
    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }

    public function jars(): HasMany
    {
        return $this->hasMany(Jar::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Jar::class);
    }

    /**
     * Calculate the balance of the account in the UAH currency.
     *
     * @return Attribute
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    protected function uahBalance(): Attribute
    {
        $rate = $this->currency ? app(CurrencyConverter::class)->getRate(
            $this->currency,
            Currency::UAH
        ) : 1;

        return Attribute::make(
            get: fn () => round($this->balance * $rate),
        );
    }
}
