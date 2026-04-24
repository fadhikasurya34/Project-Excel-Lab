<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionStep extends Model {
    protected $fillable = [
        'mission_id', 
        'step_image', 
        'instruction', 
        'key_answer_cell', 
        'step_order',
        'target_x',
        'target_y'  
    ];

    public function hotspots()
    {
        return $this->hasMany(MissionHotspot::class, 'step_id');
    }
    public function mission(): BelongsTo
    {
    return $this->belongsTo(Mission::class);
    }
}