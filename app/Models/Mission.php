<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mission extends Model
{
    protected $fillable = [
        'title',        
        'mission_type',
        'question',
        'key_answer',
        'max_score',
        'level_id',
        'mission_image', 
        'target_x',
        'target_y',
        'distractors'
    ];
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
    public function steps() 
    {
        return $this->hasMany(MissionStep::class)->orderBy('step_order');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_mission');
    }
}