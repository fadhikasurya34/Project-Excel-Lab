<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialActivity;
use App\Models\MaterialCategory; 
use App\Models\Hotspot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class AdminMateriController extends Controller
{
    /** (View) Menampilkan statistik & daftar semua modul untuk di-grouping di Blade */
    public function adminDashboard() {
        $stats = [
            'total_materi'  => Material::count(),
            'total_langkah' => MaterialActivity::count(),
            'total_siswa'   => User::where('role', 'siswa')->count(),
            'total_misi'    => \App\Models\Mission::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    /**  (View) Menampilkan DAFTAR FOLDER (Topik)*/
    public function index() {
        // WithCount untuk menghitung relasi, diurutkan agar stabil
        $categories = MaterialCategory::withCount('materials')->orderBy('id', 'asc')->get();

        $stats = [
            'total_materi'  => Material::count(),
            'total_langkah' => MaterialActivity::count(),
            'total_siswa'   => User::where('role', 'siswa')->count(),
            'total_misi'    => \App\Models\Mission::count(),
        ];

        return view('admin.materials.index', compact('categories', 'stats'));
    }

    /**(View) Menampilkan daftar materi di dalam satu kategori (Folder) 
     */
    public function listByTopic(string $id) {
        $category = MaterialCategory::findOrFail($id);
        
        // FIX: Tarik data melalui relasi Eloquent agar konsisten dan tidak ada yang hilang
        $materials = $category->materials()
            ->withCount('activities')
            ->orderBy('created_at', 'asc') // Urutkan berdasarkan waktu pembuatan
            ->get();

        $allTopics = MaterialCategory::orderBy('id', 'asc')->get();
        
        $stats = [
            'total_materi'  => Material::count(),
            'total_langkah' => MaterialActivity::count(),
            'total_siswa'   => User::where('role', 'siswa')->count(),
            'total_misi'    => \App\Models\Mission::count(),
        ];

        return view('admin.materials.topic', [
            'materials' => $materials,
            'category' => $category->name, 
            'allTopics' => $allTopics,
            'topicData' => $category,
            'category_id' => $category->id,
            'stats' => $stats 
        ]);
    }

    /** (View) Form pembuatan modul materi baru */
    public function create() { 
        return view('admin.materials.create'); 
    }

    /** (Action) Simpan FOLDER BARU (Topik) */
    public function storeTopic(Request $request) {
        $request->validate([
            'name'        => 'required|string|max:255', 
            'description' => 'required',
        ]);

        MaterialCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.materials.index')
                         ->with('success', 'Folder Topik berhasil dibuat!');
    }

    /** (Action) Update Informasi Folder */
    public function updateTopic(Request $request, string $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required'
        ]);
        $category = MaterialCategory::findOrFail($id);
        $category->update($request->only('name', 'description'));
        return back()->with('success', 'Folder berhasil diperbarui.');
    }

    /** (Action) Hapus Folder */
    public function destroyTopic(string $id){
        $category = MaterialCategory::findOrFail($id);
        $category->delete(); 
        return back()->with('success', 'Folder beserta isinya berhasil dihapus.');
    }

    /** (Action) Simpan materi secara cepat dari halaman Topik 
     */
    public function storeQuick(Request $request) {
        $request->validate([
            'title'         => 'required|string|max:255',
            'category_id'   => 'nullable|exists:material_categories,id',
            'category'      => 'nullable|string',
            'material_type' => 'required|in:teori,praktikum',
            'description'   => 'required|string'
        ]);

        $categoryId = $request->category_id;
        if (!$categoryId && $request->category) {
            $cat = MaterialCategory::where('name', $request->category)->first();
            $categoryId = $cat ? $cat->id : null;
        }

        Material::create([
            'title'         => $request->title,
            'category_id'   => $categoryId,
            'material_type' => $request->material_type,
            'description'   => $request->description,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan ke folder ini.');
    }

    /** (View) Form edit identitas modul materi */
    public function edit(string $id) {
        $material = Material::findOrFail($id);
        return view('admin.materials.edit', compact('material'));
    }

    /** (Action) Memperbarui informasi modul */
    public function update(Request $request, string $id) {
        $request->validate([
            'title'           => 'required|string|max:255', 
            'description'     => 'required',
            'category_id'     => 'nullable|exists:material_categories,id', 
            'material_type'   => 'required'
        ]);

        $material = Material::findOrFail($id);
        
        $material->update([
            'title'         => $request->title,
            'description'   => $request->description,
            'category_id'   => $request->category_id ?? $material->category_id,
            'material_type' => $request->material_type
        ]);
        
        return back()->with('success', 'Identitas modul diperbarui.');
    }

    /** (Action) Menghapus modul secara total */
    public function destroy(string $id) {
        DB::transaction(function () use ($id) {
            $material = Material::with('activities.hotspots')->findOrFail($id);
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));

            foreach ($material->activities as $step) {
                foreach ($step->hotspots as $hs) {
                    if ($hs->video_path) $uploadApi->destroy($this->getPublicId($hs->video_path), ['resource_type' => 'video']);
                }
                if ($step->step_image && !str_contains($step->step_image, 'drive.google.com')) {
                    try { $uploadApi->destroy($this->getPublicId($step->step_image)); } catch(\Exception $e){}
                }
            }
            $material->delete();
        });
        return back()->with('success', 'Modul berhasil dihapus.');
    }

    /** (View) Menampilkan editor sesuai tipe materi */
    public function showSteps(string $id) {
        $material = Material::with(['activities' => fn($q) => $q->orderBy('step_order', 'asc')])->findOrFail($id);

        if ($material->material_type === 'teori') {
            return view('admin.materials.theory', compact('material'));
        }

        return view('admin.materials.steps', compact('material'));
    }

    /** (Action) Unggah Langkah (Hybrid Support) */
    public function storeStep(Request $request, string $id) {
        $material = Material::findOrFail($id);

        $request->validate([
            'image'        => 'nullable|image|max:2048',
            'external_url' => 'nullable|url',
            'instruction'  => 'nullable'
        ]);

        $finalUrl = null;

        if ($request->hasFile('image')) {
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
            $upload = $uploadApi->upload($request->file('image')->getRealPath(), [
                'folder' => 'materials/steps'
            ]);
            $finalUrl = $upload['secure_url'];
        } 
        elseif ($request->filled('external_url')) {
            $url = $request->external_url;
            if (str_contains($url, 'drive.google.com')) {
                $url = str_replace(['/view?usp=sharing', '/view'], '/preview', $url);
            }
            $finalUrl = $url;
        }

        if (!$finalUrl) {
            return back()->with('error', 'Unggah gambar atau masukkan link external!');
        }

        if ($material->material_type === 'teori') {
            MaterialActivity::updateOrCreate(
                ['material_id' => $id],
                [
                    'step_image'  => $finalUrl,
                    'instruction' => $request->instruction ?? 'Ringkasan materi.',
                    'step_order'  => 1
                ]
            );
        } else {
            MaterialActivity::create([
                'material_id' => $id,
                'step_image'  => $finalUrl,
                'instruction' => $request->instruction ?? 'Perhatikan bagian ini.',
                'step_order'  => MaterialActivity::where('material_id', $id)->count() + 1,
            ]);
        }

        return back()->with('success', 'Konten materi berhasil diperbarui.');
    }

    /** (Action) Update Langkah Materi */
    public function updateStep(Request $request, string $id) {
        $step = MaterialActivity::findOrFail($id);
        
        $request->validate([
            'image'        => 'nullable|image|max:2048',
            'external_url' => 'nullable|url',
            'instruction'  => 'required'
        ]);

        if ($request->hasFile('image')) {
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
            
            if ($step->step_image && !str_contains($step->step_image, 'drive.google.com')) {
                try { $uploadApi->destroy($this->getPublicId($step->step_image)); } catch(\Exception $e){}
            }

            $upload = $uploadApi->upload($request->file('image')->getRealPath(), [
                'folder' => 'materials/steps'
            ]);
            $step->step_image = $upload['secure_url'];
        } 
        elseif ($request->filled('external_url')) {
            $url = $request->external_url;
            if (str_contains($url, 'drive.google.com')) {
                $url = str_replace(['/view?usp=sharing', '/view'], '/preview', $url);
            }
            $step->step_image = $url;
        }

        $step->instruction = $request->instruction;
        $step->save();

        return back()->with('success', 'Langkah materi berhasil diperbarui.');
    }

    /** (Process) Sinkronisasi urutan langkah */
    public function reorderSteps(Request $request) {
        try {
            $order = $request->order;
            foreach ($order as $index => $id) {
                MaterialActivity::where('id', $id)->update(['step_order' => $index + 1]);
            }
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }

    /** (Action) Menghapus satu langkah materi */
    public function destroyStep(string $id) {
        $step = MaterialActivity::with('hotspots')->findOrFail($id);
        $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));

        foreach ($step->hotspots as $hs) {
            if ($hs->video_path) $uploadApi->destroy($this->getPublicId($hs->video_path), ['resource_type' => 'video']);
        }
        
        if ($step->step_image && !str_contains($step->step_image, 'drive.google.com')) {
            try { $uploadApi->destroy($this->getPublicId($step->step_image)); } catch(\Exception $e){}
        }
        
        $step->delete();
        return back()->with('success', 'Langkah dihapus.');
    }

    /** (View) Editor visual hotspot */
    public function builder(string $stepId) {
        $step = MaterialActivity::with(['hotspots' => fn($q) => $q->orderBy('order', 'asc')])->findOrFail($stepId);
        $material = Material::findOrFail($step->material_id);
        return view('admin.materials.builder', compact('step', 'material'));
    }

    /** (Action) Simpan hotspot */
    public function storeHotspot(Request $request)
    {
        $request->validate([
            'content'   => 'required',
            'video'     => 'nullable|mimes:mp4,mov,avi|max:51200',
            'step_id'   => 'required',
            'x_percent' => 'required',
            'y_percent' => 'required'
        ]);
        
        $videoPath = null;
        if ($request->hasFile('video')) {
                set_time_limit(600); 
                $config = Configuration::instance(env('CLOUDINARY_URL'));
                $config->api->uploadTimeout = 600;
                $config->api->connectionTimeout = 600;

                $uploadApi = new UploadApi($config);
                $upload = $uploadApi->upload($request->file('video')->getRealPath(), [
                    'folder'        => 'materials/videos',
                    'resource_type' => 'video',
                    'async'         => false, 
                ]);
                $videoPath = $upload['secure_url'];
            }

        $maxOrder = Hotspot::where('material_activity_id', $request->step_id)->max('order');
        $nextOrder = is_null($maxOrder) ? 
                     Hotspot::where('material_activity_id', $request->step_id)->count() + 1 : 
                     $maxOrder + 1;

        Hotspot::create([
            'material_activity_id' => $request->step_id,
            'x_percent'            => $request->x_percent,
            'y_percent'            => $request->y_percent,
            'content'              => $request->content,
            'video_path'           => $videoPath,
            'type'                 => $videoPath ? 'video' : 'text',
            'order'                => $nextOrder
        ]);

        return back()->with('success', 'Titik interaksi dan video berhasil disimpan!');
    }

    /** (Process) Update urutan hotspot */
    public function reorderHotspots(Request $request) {
        try {
            $order = $request->order;
            foreach ($order as $index => $id) {
                Hotspot::where('id', $id)->update(['order' => $index + 1]);
            }
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }

    /** (Action) Menghapus titik hotspot */
    public function destroyHotspot(string $id) {
        $hotspot = Hotspot::findOrFail($id);
        if ($hotspot->video_path) {
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
            $uploadApi->destroy($this->getPublicId($hotspot->video_path), ['resource_type' => 'video']);
        }
        $hotspot->delete();
        return back()->with('success', 'Titik hotspot dihapus.');
    }

    /** (Helper) Mengambil Public ID Cloudinary */
    private function getPublicId(string $url) {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', $path);
        $filename = end($segments);
        $folder = $segments[count($segments)-3] . '/' . $segments[count($segments)-2];
        return $folder . '/' . pathinfo($filename, PATHINFO_FILENAME);
    }
}