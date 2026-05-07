<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\MaterialCompletion;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    /** * (View) Menampilkan daftar FOLDER/TOPIK materi
     * Data: Mengambil semua kategori dari tabel MaterialCategory
     */
    public function index()
    {
        // Mengambil semua folder kategori beserta jumlah modul di dalamnya
        $categories = MaterialCategory::withCount('materials')->get();
            
        return view('materi.index', compact('categories'));
    }

    /** * (View) Menampilkan daftar modul materi di dalam folder tertentu */
    public function showByCategory(string $id)
    {
        $category = MaterialCategory::findOrFail($id);

        $materials = Material::where('category_id', $id)
            ->withCount('activities')
            ->orderBy('id', 'asc')
            ->get();

        $userProgress = MaterialCompletion::where('user_id', Auth::id())
            ->pluck('material_id')
            ->toArray();

        // FIX: Pastikan nama view ini 'materi.category'
        return view('materi.category', compact('materials', 'category', 'userProgress'));
    }

    /** * (View) Player Materi: Menampilkan detail konten
     * Logika: Memisahkan tampilan antara Teori (PDF/Video) dan Praktikum (Hotspot)
     */
    public function show(string $id)
    {
        // Eager loading activities dan hotspots agar performa cepat
        $material = Material::with(['activities.hotspots' => function($q) {
            $q->orderBy('order', 'asc');
        }])->findOrFail($id);

        // Logika Progres: Tandai "Selesai" otomatis jika siswa membuka materi ini
        if (Auth::check() && Auth::user()->role !== 'admin') {
            MaterialCompletion::firstOrCreate([
                'user_id'     => Auth::id(),
                'material_id' => $id
            ]);
        }

        // PENGALIHAN VIEW BERDASARKAN TIPE
        // 1. Tipe Teori: Diarahkan ke blade khusus materi bacaan/video
        if ($material->material_type === 'teori') {
            return view('materi.theory', compact('material'));
        }

        // 2. Tipe Praktikum: Diarahkan ke blade interaktif (Hotspot/Virtual Lab)
        return view('materi.show', compact('material'));
    }
}