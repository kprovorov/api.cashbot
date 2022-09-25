<?php

namespace App\PaymentModule\Models;

use App\Enums\Currency;
use App\Models\Group;
use App\Models\Jar;
use App\Models\Transfer;
use App\PaymentModule\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jar_id',
        'group_id',
        'description',
        'amount',
        'amount_converted',
        'currency',
        'date',
        'hidden',
        'ends_on',
    ];

    protected $casts = [
        'balance' => 'integer',
        'jar_balance' => 'integer',
        'jar_savings_balance' => 'integer',
        'currency' => Currency::class,
        'date' => 'date',
        'ends_on' => 'date',
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
     * @return PaymentFactory
     */
    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }

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
