<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Jar
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $account_id
 * @property string $name
 * @property int $default
 * @property-read \App\Models\Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read int|null $payments_count
 *
 * @method static \Database\Factories\JarFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Jar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Jar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Jar query()
 * @method static \Illuminate\Database\Eloquent\Builder|Jar whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jar whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jar whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jar whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Jar extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
