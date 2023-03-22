<?php

namespace App\AccountModule\Models;

use App\AccountModule\Factories\AccountFactory;
use App\Enums\Currency;
use App\PaymentModule\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\AccountModule\Models\Account
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $name
 * @property int $balance
 * @property Currency $currency
 * @property string|null $provider_id
 * @property string|null $provider
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Account> $jars
 * @property-read int|null $jars_count
 * @property-read Account|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments_from
 * @property-read int|null $payments_from_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments_to
 * @property-read int|null $payments_to_count
 *
 * @method static \App\AccountModule\Factories\AccountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUserId($value)
 *
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
        'parent_id',
        'user_id',
        'name',
        'currency',
        'balance',
        'provider_id',
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
        'currency' => Currency::class,
        'balance' => 'int',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }

    public function payments_from(): HasMany
    {
        return $this->hasMany(Payment::class, 'account_from_id');
    }

    public function payments_to(): HasMany
    {
        return $this->hasMany(Payment::class, 'account_to_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function jars(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }
}
