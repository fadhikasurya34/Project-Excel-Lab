<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['name', 'teacher_name', 'class_code', 'icon'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'classroom_user')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
