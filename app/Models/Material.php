<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'category'];

    public function activities()
    {
        return $this->hasMany(MaterialActivity::class)->orderBy('step_order');
    } 

    public function completions()
    {
        return $this->hasMany(MaterialCompletion::class);
    }
}