<?php

namespace App\Models;

use App\Enums\Currency;
use App\PaymentModule\Models\Payment;
use App\Services\CurrencyConverter;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'balance',
    ];

    protected $casts = [
        'balance' => 'int',
        'uah_balance' => 'int',
        'currency' => Currency::class,
    ];

    /**
     * @return HasMany
     */
    public function jars(): HasMany
    {
        return $this->hasMany(Jar::class);
    }

    /**
     * @return HasManyThrough
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Jar::class);
    }

    /**
     * Calculate the balance of the account in the UAH currency.
     *
     * @return Attribute
     *
     * @throws Exception
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
