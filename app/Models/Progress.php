<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progress extends Model
{
    protected $table = 'progress';

    protected $fillable = ['user_id', 'mission_id', 'status', 'score', 'completion_time', 'attempts'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
    protected $casts = [
        'completion_time' => 'datetime',
    ];
}