<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['category', 'level_name', 'level_order', 'description'];

    public function missions()
    {
        return $this->hasMany(Mission::class);
    }
}