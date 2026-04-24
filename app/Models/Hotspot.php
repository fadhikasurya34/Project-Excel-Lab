<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    // Tambahan 'order' wajib ada karena dipakai di AdminMateriController
    protected $fillable = ['material_activity_id', 'x_percent', 'y_percent', 'content', 'video_path', 'type', 'order'];

    public function activity()
    {
        return $this->belongsTo(MaterialActivity::class, 'material_activity_id');
    }
}