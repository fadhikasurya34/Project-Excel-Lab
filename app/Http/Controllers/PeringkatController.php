<?php

namespace App\Http\Controllers;

use App\Models\ScoresAndRanking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeringkatController extends Controller
{
    /** (View) Menampilkan halaman utama pemilihan kategori peringkat */
    public function index()
    {
        return view('peringkat.index');
    }

    /** (View) Menampilkan daftar leaderboard berdasarkan XP tertinggi (Global/Kelas) */
    public function show($type)
    {
        // 1. Logika: Menggunakan Eager Loading 'user' agar performa database efisien (anti N+1 Query)
        $query = ScoresAndRanking::with('user')->orderBy('total_xp', 'desc');

        // 2. Filter: Logika khusus untuk membatasi peringkat hanya di dalam satu kelas
        if ($type === 'kelas') {
            $userClassId = Auth::user()->class_id; 
            
            if (!$userClassId) {
                return redirect()->back()->with('error', 'Kamu belum terdaftar di kelas manapun.');
            }

            $query->whereHas('user', function($q) use ($userClassId) {
                $q->where('class_id', $userClassId);
            });
        }

        // 3. Eksekusi query untuk mengambil data ranking
        $rankings = $query->get();

        return view('peringkat.show', compact('rankings', 'type'));
    }
}