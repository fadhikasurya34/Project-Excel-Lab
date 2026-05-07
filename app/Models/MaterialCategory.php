<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Relasi agar bisa menghitung jumlah isi folder
    public function materials()
    {
        return $this->hasMany(Material::class, 'category_id');
    }
}