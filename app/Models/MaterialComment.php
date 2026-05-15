<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialComment extends Model
{
    protected $fillable = ['material_id', 'user_id', 'body', 'parent_id', 'likes', 'dislikes'];

    public function replies() {
        return $this->hasMany(MaterialComment::class, 'parent_id')->oldest();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}