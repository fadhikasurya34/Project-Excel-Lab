<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialActivity;
use App\Models\Hotspot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cloudinary\Configuration\Configuration; // Import Native Config
use Cloudinary\Api\Upload\UploadApi; // Import Native Upload

class AdminMateriController extends Controller
{
    /** (View) Menampilkan statistik ringkasan data di dashboard utama admin */
    public function adminDashboard() {
        $stats = [
            'total_materi'  => Material::count(),
            'total_langkah' => MaterialActivity::count(),
            'total_siswa'   => User::where('role', 'siswa')->count(),
            'total_misi'    => \App\Models\Mission::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    /** (View) Menampilkan daftar modul materi */
    public function index() {
        $materials = Material::withCount('activities')->get();
        return view('admin.materials.index', compact('materials'));
    }

    /** (View) Form pembuatan modul materi baru */
    public function create() { 
        return view('admin.materials.create'); 
    }

    /** (Action) Menyimpan data dasar modul materi */
    public function store(Request $request) {
        $request->validate([
            'title'       => 'required|string|max:255', 
            'description' => 'required',
            'category'    => 'required|in:materi,praktik'
        ]);

        $material = Material::create([
            'title'            => $request->title,
            'description'      => $request->description,
            'category'         => $request->category,
            'material_type'    => 'instruksional',
            'background_image' => null 
        ]);

        return redirect()->route('admin.materials.steps', $material->id)
                         ->with('success', 'Modul berhasil dibuat!');
    }

    /** (View) Form edit identitas modul materi */
    public function edit($id) {
        $material = Material::findOrFail($id);
        return view('admin.materials.edit', compact('material'));
    }

    /** (Action) Memperbarui informasi dasar modul */
    public function update(Request $request, $id) {
        $request->validate([
            'title'       => 'required|string|max:255', 
            'description' => 'required',
            'category'    => 'required|in:materi,praktik'
        ]);

        $material = Material::findOrFail($id);
        $material->update($request->only('title', 'description', 'category'));
        
        return redirect()->route('admin.materials.index')->with('success', 'Identitas modul diperbarui.');
    }

    /** (Action) Menghapus modul secara total */
    public function destroy($id) {
        DB::transaction(function () use ($id) {
            $material = Material::with('activities.hotspots')->findOrFail($id);
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));

            foreach ($material->activities as $step) {
                foreach ($step->hotspots as $hs) {
                    if ($hs->video_path) $uploadApi->destroy($this->getPublicId($hs->video_path), ['resource_type' => 'video']);
                }
                if ($step->step_image) $uploadApi->destroy($this->getPublicId($step->step_image));
            }
            $material->delete();
        });
        return redirect()->route('admin.materials.index')->with('success', 'Modul materi dihapus total.');
    }

    /** (View) Menampilkan urutan storyboard*/
    public function showSteps($id) {
        $material = Material::with(['activities' => fn($q) => $q->orderBy('step_order', 'asc')])->findOrFail($id);
        return view('admin.materials.steps', compact('material'));
    }

    /** (Action) Unggah gambar ke Cloudinary menggunakan Native SDK */
    public function storeStep(Request $request, $id) {
        $request->validate([
            'image'       => 'required|image|max:2048',
            'instruction' => 'nullable'
        ]);

        // Inisialisasi Cloudinary secara manual (Bypass Provider yang error)
        $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
        
        $upload = $uploadApi->upload($request->file('image')->getRealPath(), [
            'folder' => 'materials/steps'
        ]);

        MaterialActivity::create([
            'material_id' => $id,
            'step_image'  => $upload['secure_url'],
            'instruction' => $request->instruction ?? 'Perhatikan bagian ini.',
            'step_order'  => MaterialActivity::where('material_id', $id)->count() + 1,
        ]);

        return back()->with('success', 'Langkah materi ditambahkan.');
    }

    /** (Process) Sinkronisasi urutan langkah (AJAX) via Drag & Drop */
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
    public function destroyStep($id) {
        $step = MaterialActivity::with('hotspots')->findOrFail($id);
        $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));

        foreach ($step->hotspots as $hs) {
            if ($hs->video_path) $uploadApi->destroy($this->getPublicId($hs->video_path), ['resource_type' => 'video']);
        }
        if ($step->step_image) $uploadApi->destroy($this->getPublicId($step->step_image));
        
        $step->delete();
        return back()->with('success', 'Langkah dihapus.');
    }

    /** (View) Editor visual untuk menempatkan titik hotspot interaktif */
    public function builder($stepId) {
        $step = MaterialActivity::with(['hotspots' => fn($q) => $q->orderBy('order', 'asc')])->findOrFail($stepId);
        $material = Material::findOrFail($step->material_id);
        return view('admin.materials.builder', compact('step', 'material'));
    }

    /** (Action) Simpan hotspot & upload video ke Cloudinary (Native Chunked) */
    public function storeHotspot(Request $request)
    {
        $request->validate([
            'content'   => 'required',
            'video'     => 'nullable|mimes:mp4,mov,avi|max:51200', // Max 50MB
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

    /** (Process) Update urutan tampilan hotspot (AJAX) */
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
    public function destroyHotspot($id) {
        $hotspot = Hotspot::findOrFail($id);
        if ($hotspot->video_path) {
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
            $uploadApi->destroy($this->getPublicId($hotspot->video_path), ['resource_type' => 'video']);
        }
        $hotspot->delete();
        return back()->with('success', 'Titik hotspot dihapus.');
    }

    /** (Helper) Mengambil Public ID dari URL Cloudinary untuk proses hapus */
    private function getPublicId($url) {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', $path);
        $filename = end($segments);
        $folder = $segments[count($segments)-3] . '/' . $segments[count($segments)-2];
        return $folder . '/' . pathinfo($filename, PATHINFO_FILENAME);
    }
}