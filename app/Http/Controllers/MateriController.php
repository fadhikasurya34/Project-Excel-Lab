<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\MaterialCompletion;
use App\Models\MaterialComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    /** (View) Menampilkan daftar kategori/topik materi */
    public function index()
    {
        $categories = MaterialCategory::withCount('materials')->get();
        return view('materi.index', compact('categories'));
    }

    /** (View) Menampilkan daftar modul materi dalam kategori tertentu */
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

        return view('materi.category', compact('materials', 'category', 'userProgress'));
    }

    /** (View) Menampilkan detail konten materi */
    public function show(string $id)
    {
        $material = Material::with([
            'activities.hotspots' => function($q) {
                $q->orderBy('order', 'asc');
            },
            'comments' => function($q) {
                $q->whereNull('parent_id')
                  ->with(['user', 'replies.user'])
                  ->latest(); 
            }
        ])->findOrFail($id);

        if (Auth::check() && Auth::user()->role !== 'admin') {
            MaterialCompletion::firstOrCreate([
                'user_id'     => Auth::id(),
                'material_id' => $id
            ]);
        }

        if ($material->material_type === 'teori') {
            return view('materi.theory', compact('material'));
        }

        return view('materi.show', compact('material'));
    }

    /** (Action) Menyimpan komentar atau balasan baru ke database */
    public function storeComment(Request $request, string $id)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:material_comments,id'
        ]);

        MaterialComment::create([
            'material_id' => $id,
            'user_id'     => Auth::id(),
            'body'        => $request->body,
            'parent_id'   => $request->parent_id
        ]);

        return back()->with('success', 'Komentar berhasil dikirim!');
    }

    /** (Action) Menangani reaksi Like atau Dislike pada komentar */
    public function reactComment(string $id, string $type)
    {
        $comment = MaterialComment::findOrFail($id);

        if ($type === 'like') {
            $comment->increment('likes');
        } elseif ($type === 'dislike') {
            $comment->increment('dislikes');
        }

        return back();
    }
    
    /** (Action) Menghapus komentar atau balasan milik pengguna */
    public function destroyComment(string $id)
    {
        $comment = \App\Models\MaterialComment::findOrFail($id);
        
        // Proteksi: Pastikan hanya pemilik yang bisa menghapus
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $comment->delete();
        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}