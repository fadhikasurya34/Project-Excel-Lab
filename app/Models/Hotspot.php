<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    protected $fillable = ['material_activity_id', 'x_percent', 'y_percent', 'content', 'video_path', 'type'];

    public function activity()
    {
        return $this->belongsTo(MaterialActivity::class, 'material_activity_id');
    }
}