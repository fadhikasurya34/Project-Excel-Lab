<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Mission; 
use App\Models\Task;   
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AdminClassroomController extends Controller
{
    /** (View) Menampilkan daftar kelas */
    public function index()
    {
        $classrooms = Classroom::withCount('users')->latest()->get();
        return view('admin.classrooms.index', compact('classrooms'));
    }

    /** (Action) Membuat kelas baru */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'teacher_name' => 'required|string|max:255',
        ]);

        $code = 'EXCEL-' . strtoupper(Str::random(4));

        Classroom::create([
            'name' => $request->name,
            'teacher_name' => $request->teacher_name,
            'class_code' => $code,
            'icon' => '🏛️' 
        ]);

        return back()->with('success', 'Squad baru berhasil dibuat.');
    }

    /** (View) Detail Kelas & Pengambilan Nilai */
    public function show($id)
    {
        try {
            // Eager Loading relasi yang benar agar data tersedia untuk pemetaan Alpine.js
            $classroom = Classroom::with([
                'users.ranking',          // Mengambil data dari tabel scores_and_rankings
                'users.progress',         // Mengambil riwayat misi (untuk hitung manual jika ranking 0)
                'users.completedMaterials', // Nama yang benar sesuai Model User kamu
                'tasks.missions' 
            ])
            ->withCount(['users']) 
            ->findOrFail($id);
            
            // Ambil semua misi untuk checklist 'Ambil Nilai'
            $availableMissions = Mission::orderBy('title', 'asc')->get();

            return view('admin.classrooms.show', compact('classroom', 'availableMissions'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error pada AdminClassroomController@show: " . $e->getMessage());
            
            return redirect()->route('admin.classrooms.index')
                            ->with('error', 'Gagal memuat data kelas: ' . $e->getMessage());
        }
    }

    /** (Action) Menyimpan Checklist Misi menjadi Task Permanen */
    public function storeTask(Request $request, $id)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'mission_ids' => 'required|array',
            'mission_ids.*' => 'exists:missions,id'
        ]);

        try {
            $task = Task::create([
                'classroom_id' => $id,
                'name' => $request->task_name
            ]);

            // Simpan ke tabel pivot task_mission
            $task->missions()->attach($request->mission_ids);

            return back()->with('success', 'Tugas berhasil dibuat untuk squad ini.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan tugas: ' . $e->getMessage());
        }
    }

    /** (Action) Update kelas */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'teacher_name' => 'required|string|max:255',
        ]);

        $classroom = Classroom::findOrFail($id);
        $classroom->update($request->only(['name', 'teacher_name']));

        return back()->with('success', 'Data squad berhasil diperbarui.');
    }

    /** (Action) Hapus kelas */
    public function destroy($id)
    {
        Classroom::findOrFail($id)->delete();
        return back()->with('success', 'Squad kelas telah dihapus.');
    }

    /** (Action) Kick siswa */
    public function kick($id, $userId)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->users()->detach($userId);
        return back()->with('success', 'Siswa berhasil dikeluarkan.');
    }
    /** (Action) Update Nama Task */
    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update(['name' => $request->task_name]);
        return back()->with('success', 'Nama tugas berhasil diperbarui.');
    }

    /** (Action) Hapus Task */
    public function destroyTask($id)
    {
        $task = Task::findOrFail($id);
        $task->missions()->detach(); // Hapus relasi di pivot
        $task->delete();
        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    /** (Action) Export Nilai Task ke Excel/CSV */
    public function exportTask($id)
    {
        $task = Task::with(['missions', 'classroom.users.progress'])->findOrFail($id);
        $missionIds = $task->missions->pluck('id');
        $maxScore = $task->missions->sum('max_score');

        $fileName = 'Nilai_' . str_replace(' ', '_', $task->name) . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Gunakan titik koma (;) sebagai delimiter agar Excel Indonesia langsung rapi
        $callback = function() use($task, $missionIds, $maxScore) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM (Byte Order Mark) agar Excel mengenali UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Tabel
            fputcsv($file, ['Nama Praktikan', 'Email', 'Total XP', 'Skor Akhir (1-100)'], ';');

            foreach ($task->classroom->users as $user) {
                $userScore = $user->progress->whereIn('mission_id', $missionIds)->sum('score');
                
                // Hitung nilai skala 100
                $finalGrade = $maxScore > 0 ? round(($userScore / $maxScore) * 100, 2) : 0;

                // Isi baris data dengan pemisah titik koma
                fputcsv($file, [
                    $user->name, 
                    $user->email, 
                    $userScore, 
                    str_replace('.', ',', $finalGrade)
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}