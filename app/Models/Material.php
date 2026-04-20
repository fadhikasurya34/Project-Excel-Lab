<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'category', 'material_type', 'background_image'];

    public function activities()
    {
        return $this->hasMany(MaterialActivity::class);
    }
}