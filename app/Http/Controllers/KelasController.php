<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Task; 
use App\Models\Progress; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    /** (View) Menampilkan daftar seluruh kelas yang telah diikuti oleh siswa */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $classrooms = $user->classrooms; 

        return view('kelas.index', compact('classrooms'));
    }

    /** (View) Menampilkan leaderboard internal kelas dan daftar Task */
    public function show($id)
    {
        // Logika: Load kelas, user (dengan ranking untuk XP), dan tasks yang dibuat admin
        $classroom = Classroom::with([
            'users.ranking', 
            'tasks.missions'
        ])->findOrFail($id);

        // Mengurutkan user berdasarkan XP tertinggi (menggunakan collection sorting karena XP adalah accessor)
        $sortedUsers = $classroom->users->sortByDesc(function($user) {
            return $user->total_xp;
        });

        // Menentukan peringkat user yang sedang login
        $currentUserRank = $sortedUsers->pluck('id')->search(Auth::id()) + 1;

        return view('kelas.show', compact('classroom', 'currentUserRank'));
    }

    /** * (NEW View) Menampilkan detail isi Task (Daftar Misi Pilihan Admin)
     */
    public function showTask($id)
    {
        $task = Task::with(['missions.level', 'classroom'])->findOrFail($id);
        
        /** @var \App\Models\User $user */
        $user = Auth::user(); 

        $missionIds = $task->missions->pluck('id');

        $userProgress = Progress::where('user_id', $user->id)
                        ->whereIn('mission_id', $missionIds)
                        ->get()
                        ->keyBy('mission_id');

        return view('kelas.task-detail', compact('task', 'userProgress'));
    }

    /** (Action) Memproses pendaftaran kelas baru */
    public function store(Request $request)
    {
        $request->validate([
            'class_code' => 'required|string|exists:classrooms,class_code',
        ], [
            'class_code.exists' => 'Kode kelas tidak ditemukan di sistem.'
        ]);

        $classroom = Classroom::where('class_code', $request->class_code)->first();
        /** @var User $user */
        $user = Auth::user();

        if ($user->classrooms()->where('classroom_id', $classroom->id)->exists()) {
            return back()->with('error', 'Kamu sudah bergabung di kelas ini.');
        }

        $user->classrooms()->attach($classroom->id);

        return redirect()->route('kelas.index')->with('success', 'Berhasil bergabung dengan squad!');
    }
}