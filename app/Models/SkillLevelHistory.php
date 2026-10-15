<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillLevelHistory extends Model
{
    protected $fillable = ['skill_id', 'old_level', 'new_level'];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}