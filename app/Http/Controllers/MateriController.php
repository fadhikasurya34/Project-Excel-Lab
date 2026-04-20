<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    /** (View) Menampilkan daftar seluruh modul materi yang tersedia */
    public function index()
    {
        $materials = Material::all();
        return view('materi.index', compact('materials'));
    }

    /** (View) Menampilkan detail simulasi interaktif dan mencatat riwayat pengerjaan siswa */
    public function show($id)
    {
        $material = Material::with('activities.hotspots')->findOrFail($id);

        // Logika: Mencatat otomatis jika yang login adalah siswa (Mencegah duplikasi log)
        if (Auth::user()->role !== 'admin') {
            MaterialCompletion::firstOrCreate([
                'user_id'     => Auth::id(),
                'material_id' => $id
            ]);
        }

        return view('materi.show', compact('material'));
    }
}