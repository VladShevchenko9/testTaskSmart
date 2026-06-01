<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const string STATUS_NEW = 'new';
    public const string STATUS_IN_PROGRESS = 'in_progress';
    public const string STATUS_PROCESSED = 'processed';

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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
