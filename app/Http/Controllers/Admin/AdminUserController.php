<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Progress;
use App\Models\ScoresAndRanking;
use App\Models\RetryTicket;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /** (View) Menampilkan daftar siswa beserta peringkat dan kelasnya */
    public function index()
    {
        // Ambil user yang bukan admin
        $students = User::where('role', '!=', 'admin')
            ->with(['ranking', 'classrooms'])
            ->latest()
            ->get();

        return view('admin.users.index', compact('students'));
    }

    /** (Action) Reset total XP, progres misi, dan mengembalikan tiket remedial hari ini */
    public function resetXP(string $id)
    {
        return DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);

            // 1. Reset Total XP ke 0
            ScoresAndRanking::updateOrCreate(
                ['user_id' => $user->id],
                ['total_xp' => 0]
            );

            // 2. Hapus semua riwayat penyelesaian misi
            Progress::where('user_id', $user->id)->delete();

            // 3. Reset tiket remedial hari ini agar kembali menjadi utuh
            RetryTicket::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->update(['used_count' => 0]);

            return back()->with('success', "XP dan riwayat misi {$user->name} telah dikosongkan secara total.");
        });
    }

    /** (Action) Mereset kuota tiket retry harian untuk siswa spesifik (Tanpa menghapus XP) */
    public function resetRetryTickets(string $id)
    {
        $today = now()->toDateString();
        
        // Ubah jumlah penggunaan tiket hari ini menjadi 0 (Siswa kembali punya 3 tiket)
        RetryTicket::where('user_id', $id)
            ->where('date', $today)
            ->update(['used_count' => 0]);

        return back()->with('success', 'Jatah tiket remedial siswa hari ini berhasil di-reset menjadi 3.');
    }

    /** (Action) Menghapus akun siswa secara total beserta data terkait */
    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
            
            Progress::where('user_id', $user->id)->delete();
            ScoresAndRanking::where('user_id', $user->id)->delete();
            RetryTicket::where('user_id', $user->id)->delete();
            
            $user->delete();

            return back()->with('success', "Akun siswa berhasil dihapus secara permanen dari sistem.");
        });
    }

    /** (View) Menampilkan profil detail, riwayat misi, dan progres materi siswa */
    public function show(string $id)
    {
        $student = User::with(['ranking', 'classrooms'])->findOrFail($id);

        // Ambil riwayat penyelesaian misi
        $completedMissions = $student->progress()
            ->with('mission.level')
            ->where('status', 'completed')
            ->latest('completion_time')
            ->get();

        // Ambil riwayat materi
        $completedMaterials = $student->completedMaterials()
            ->with('material') 
            ->latest()
            ->get();

        return view('admin.users.show', compact('student', 'completedMissions', 'completedMaterials'));
    }

    /** (Action) Menghapus progres satu misi dan Recalculate XP secara otomatis */
    public function destroyMissionProgress(string $id)
    {
        return DB::transaction(function () use ($id) {
            $progress = Progress::findOrFail($id);
            $userId = $progress->user_id;

            // 1. Hapus riwayat progres misi yang dipilih
            $progress->delete();

            // 2. RECALCULATION METHOD: Hitung ulang XP dari sisa misi yang ada
            $totalXpNow = Progress::where('user_id', $userId)
                ->where('status', 'completed')
                ->sum('score');

            // 3. Timpa tabel ranking dengan hasil perhitungan baru
             ScoresAndRanking::updateOrCreate(
                ['user_id' => $userId],
                ['total_xp' => $totalXpNow]
            );

            return back()->with('success', 'Riwayat misi dihapus. Total XP siswa telah disinkronkan ulang.');
        });
    }

    /** (Action) Menghapus riwayat pengerjaan materi individu */
    public function destroyMaterialProgress(string $id)
    {
        $progress = \App\Models\MaterialCompletion::findOrFail($id);
        $progress->delete();

        return back()->with('success', 'Riwayat aktivitas baca materi berhasil dihapus.');
    }
}