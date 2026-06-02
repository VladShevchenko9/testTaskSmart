<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $subject
 * @property string $message
 * @property string $status
 * @property Carbon|null $manager_reply_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $customer
 */
class Ticket extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'customer_id',
        'subject',
        'message',
        'status',
        'manager_reply_at',
    ];

    protected $casts = [
        'manager_reply_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder<Ticket> $query
     * @param Carbon $from
     * @param Carbon $to
     * @return Builder<Ticket>
     */
    public function scopeCreatedBetween(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * @param Builder<Ticket> $query
     * @param Carbon $from
     * @return Builder<Ticket>
     */
    public function scopeCreatedFrom(Builder $query, Carbon $from): Builder
    {
        return $query->where('created_at', '>=', $from);
    }
}
