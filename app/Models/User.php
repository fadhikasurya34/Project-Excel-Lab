<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** Atribut yang dapat diisi secara massal (Mass Assignment) */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'class_id',
        'avatar',
        'profile_color', 
    ];

    /** Atribut yang disembunyikan saat konversi ke Array/JSON (Keamanan) */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** (Relation) Relasi One-to-One ke data skor dan peringkat */
    public function ranking()
    {
        return $this->hasOne(ScoresAndRanking::class, 'user_id');
    }

    /** (Relation) Relasi Many-to-Many ke tabel kelas (squad) melalui tabel pivot */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_user')->withTimestamps();
    }

    /** (Relation) Relasi One-to-Many ke log aktivitas materi */
    public function materialActivities()
    {
        return $this->hasMany(MaterialActivity::class, 'user_id');
    }

    /** (Relation) Relasi One-to-Many ke progres pengerjaan misi */
    public function progress()
    {
        return $this->hasMany(Progress::class, 'user_id');
    }

    /** (Attribute) Mengambil total XP user secara dinamis dari relasi ranking */
    public function getTotalXpAttribute()
    {
        return $this->ranking?->total_xp ?? 0;
    }

    /** * (Attribute) Logika Gamifikasi: Menentukan Gelar dan Medali 
     * Berdasarkan posisi peringkat user di database secara real-time.
     */
    public function getRankStatusAttribute()
    {
        $rankPosition = \App\Models\ScoresAndRanking::orderBy('total_xp', 'desc')
            ->pluck('user_id')
            ->toArray();

        $myPos = array_search($this->id, $rankPosition);

        if ($myPos === false || $this->total_xp <= 0) {
            return [
                'title' => 'Excel Apprentice', 
                'medal' => 'newbie.png',
                'color' => 'slate'
            ];
        }

        $pos = $myPos + 1;

        if ($pos == 1) return ['title' => 'Grandmaster', 'medal' => 'rank 1.png', 'color' => 'yellow'];
        if ($pos == 2) return ['title' => 'Expert', 'medal' => 'rank 2.png', 'color' => 'slate'];
        if ($pos == 3) return ['title' => 'Challenger', 'medal' => 'rank 3.png', 'color' => 'orange'];
        if ($pos <= 5) return ['title' => 'Pro Practitioner', 'medal' => 'rank 4-5.png', 'color' => 'emerald'];
        if ($pos <= 10) return ['title' => 'Elite Scholar', 'medal' => 'rank 6-10.png', 'color' => 'blue'];
        if ($pos <= 20) return ['title' => 'Rising Star', 'medal' => 'rank 11-20.png', 'color' => 'purple'];

        return ['title' => 'Active Learner', 'medal' => 'Apprentice.png', 'color' => 'slate'];
    }
    /** (Logic) Bootstrapping model: Menetapkan role 'siswa' secara default saat registrasi */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->role = $user->role ?? 'siswa';
        });
    }

    /** (Relation) Relasi One-to-Many ke riwayat pengerjaan modul materi */
    public function completedMaterials()
    {
        return $this->hasMany(MaterialCompletion::class, 'user_id');
    }
}