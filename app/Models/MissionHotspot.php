<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionHotspot extends Model
{
    protected $table = 'mission_hotspots';


    protected $fillable = [
        'step_id',
        'x_percent',
        'y_percent',
        'content',
        'order'
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(MissionStep::class, 'step_id');
    }
}