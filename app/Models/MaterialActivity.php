<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialActivity extends Model
{
    protected $fillable = ['material_id', 'step_image', 'instruction', 'step_order'];

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class, 'material_activity_id');
    }

    public function completedMaterials()
    {
        return $this->hasMany(MaterialCompletion::class, 'user_id');
    }
}