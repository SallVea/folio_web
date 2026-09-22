<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialLink extends Model
{
    public const PLATFORMS = [
        'GitHub', 'LinkedIn', 'Instagram',
        'Twitter', 'YouTube', 'Facebook', 'Website', 'Other',
    ];

    protected $fillable = ['platform', 'url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
