<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    // FIX: Menambahkan video_url, text_content, dan pdf_url agar bisa disimpan dari Controller
    protected $fillable = [
        'title', 
        'description', 
        'category_id', 
        'material_type', 
        'video_url', 
        'text_content', 
        'pdf_url'
    ];

    // Relasi balik ke folder (kategori)
    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id');
    }

    // Fungsi activities yang sudah ada tetap biarkan
    public function activities()
    {
        return $this->hasMany(MaterialActivity::class)->orderBy('step_order');
    } 

    public function completions()
    {
        return $this->hasMany(MaterialCompletion::class);
    }

    // FIX: Menambahkan relasi ke tabel material_comments untuk fitur Ruang Diskusi
    public function comments()
    {
        return $this->hasMany(MaterialComment::class)->orderBy('created_at', 'desc');
    }
}