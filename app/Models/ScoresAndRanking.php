<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoresAndRanking extends Model
{
    protected $table = 'scores_and_rankings';

    protected $fillable = [
        'user_id',
        'class_id',
        'total_xp',
        'score_type',
        'completed_missions_count',
        'completed_modules_count'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}