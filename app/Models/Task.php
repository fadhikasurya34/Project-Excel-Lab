<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['classroom_id', 'name'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function missions()
    {
        return $this->belongsToMany(Mission::class, 'task_mission');
    }
}