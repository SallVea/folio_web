<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'level', 'category', 'sort_order'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function levelHistories(): HasMany
    {
        return $this->hasMany(SkillLevelHistory::class);
    }
}
