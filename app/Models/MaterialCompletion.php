<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialCompletion extends Model
{
    protected $fillable = ['user_id', 'material_id'];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}