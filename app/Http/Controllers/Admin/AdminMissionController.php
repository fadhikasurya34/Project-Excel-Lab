<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Level;
use App\Models\MissionStep;
use App\Models\MissionHotspot;
use App\Models\Progress;
use App\Models\ScoresAndRanking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class AdminMissionController extends Controller
{
    /**
     * (View) Dashboard statistik kategori misi dan tipe tantangan
     */
    public function index() {
        $categories = Level::whereNotNull('category')
            ->select('category')
            ->distinct()
            ->get()
            ->map(function($item) {
                $item->description = Level::where('category', $item->category)->value('description');
                
                $item->mission_count = Mission::whereHas('level', fn($q) => $q->where('category', $item->category))->count();
                
                $item->syntax_count = Mission::where('mission_type', 'Syntax Assembly')
                    ->whereHas('level', fn($q) => $q->where('category', $item->category))->count();
                    
                $item->visual_count = Mission::whereIn('mission_type', ['Point & Click', 'Direct Typing'])
                    ->whereHas('level', fn($q) => $q->where('category', $item->category))->count();
                    
                return $item;
            });

        $missions = Mission::all();
        return view('admin.missions.index', compact('categories', 'missions'));
    }

    /**
     * (View) Daftar misi per topik + Auto-repair urutan level
     */
    public function listByTopic($category) {
        $rawMissions = Mission::with('level')
            ->join('levels', 'missions.level_id', '=', 'levels.id')
            ->where('levels.category', $category)
            ->orderBy('levels.level_order', 'asc')
            ->orderBy('missions.id', 'asc')
            ->select('missions.*', 'levels.id as level_table_id')
            ->get();

        DB::transaction(function () use ($rawMissions) {
            foreach ($rawMissions as $index => $m) {
                $newSequence = $index + 1;
                DB::table('levels')->where('id', $m->level_table_id)->update([
                    'level_order' => $newSequence,
                    'level_name' => 'Level ' . $newSequence
                ]);
            }
        });

        $missions = Mission::with('level')
            ->join('levels', 'missions.level_id', '=', 'levels.id')
            ->where('levels.category', $category)
            ->orderBy('levels.level_order', 'asc') 
            ->select('missions.*') 
            ->get();

        $topicData = Level::where('category', $category)->first();
        $allTopics = Level::select('category')->distinct()->pluck('category');

        return view('admin.missions.topic', compact('missions', 'category', 'allTopics', 'topicData'));
    }

    /**
     * (Action) Update Data Topik (Nama Kategori & Deskripsi)
     */
    public function updateTopic(Request $request, $old_category) {
        $request->validate([
            'new_category' => 'required|string',
            'description' => 'nullable'
        ]);

        DB::table('levels')->where('category', $old_category)->update([
            'category' => $request->new_category,
            'description' => $request->description
        ]);

        return back()->with('success', 'Data topik berhasil diperbarui.');
    }

    /**
     * (Action) Menghapus seluruh topik beserta level dan misinya (Cloudinary Sync)
     */
    public function destroyTopic($category) {
        DB::transaction(function () use ($category) {
            $levels = Level::where('category', $category)->get();
            foreach ($levels as $level) {
                $missions = Mission::with('steps.hotspots')->where('level_id', $level->id)->get();
                foreach ($missions as $mission) {
                    $this->deleteMissionAssets($mission);
                    $mission->delete();
                }
                $level->delete();
            }
        });
        return redirect()->route('admin.missions.index')->with('success', 'Topik dan seluruh aset awan berhasil dihapus.');
    }

    /**
     * (View) Wizard Step 1: Form Topik Baru
     */
    public function create() {
        $allTopics = Level::select('category')->distinct()->pluck('category');
        return view('admin.missions.create', compact('allTopics'));
    }

    /**
     * (Action) Simpan Step 1 & Lanjut ke Step 2
     */
    public function store(Request $request) {
        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $level = DB::transaction(function () use ($request) {
            return Level::create([
                'category'    => $request->category,
                'level_name'  => 'Level 1',
                'level_order' => 1,
                'description' => $request->description,
            ]);
        });

        return redirect()->route('admin.missions.topic', $request->category)
                        ->with('success', 'Topik berhasil dibuat!');
    }

    /**
     * (View) Wizard Step 2: Form Isi Soal Misi
     */
    public function createDetail($level_id) {
        $level = Level::findOrFail($level_id);
        $category = $level->category;

        $groupedLevels = Mission::with('level')
            ->whereHas('level', fn($q) => $q->where('category', $category))
            ->get()
            ->groupBy(function($item) {
                return $item->level->category;
            });

        return view('admin.missions.levels', compact('level', 'groupedLevels'));
    }

    /**
     * (Action) Simpan Soal Misi Final
     */
    public function storeDetail(Request $request) {
        $request->validate([
            'level_id'     => 'required|exists:levels,id',
            'title'        => 'required|string|max:255',
            'mission_type' => 'required|in:Syntax Assembly,Point & Click,Direct Typing',
            'max_score'    => 'required|integer',
            'question'     => 'required'
        ]);

        Mission::create($request->all());

        return redirect()->route('admin.missions.index')
                        ->with('success', 'Misi pertama berhasil dipublikasikan!');
    }

    /**
     * (Action) Tambah misi cepat via Modal
     */
    public function storeQuick(Request $request) {
        $level = Level::create([
            'category' => $request->category,
            'level_name' => 'Level ' . (Level::where('category', $request->category)->count() + 1),
            'level_order' => Level::where('category', $request->category)->count() + 1,
            'description' => Level::where('category', $request->category)->value('description'),
        ]);

        Mission::create([
            'level_id' => $level->id,
            'title' => $request->title,
            'mission_type' => $request->mission_type,
            'max_score' => $request->max_score,
            'question' => 'Instruksi belum diatur.',
        ]);

        return back()->with('success', 'Misi ditambahkan.');
    }

    /**
     * (View) Form Edit Misi
     */
    public function edit($id) {
        $mission = Mission::with('level')->findOrFail($id);
        $allTopics = Level::select('category')->distinct()->pluck('category');
        return view('admin.missions.edit', compact('mission', 'allTopics'));
    }

    /**
     * (Action) Update Informasi Dasar Misi
     */
    public function update(Request $request, $id) {
        $mission = Mission::findOrFail($id);
        $oldMaxScore = $mission->max_score;

        $mission->update($request->only('title', 'mission_type', 'max_score'));

        if ($oldMaxScore != $request->max_score) {
            $this->syncUserXpForMission($mission->id);
        }

        return back()->with('success', 'Data misi diperbarui.');
    }

    /**
     * (Action) Hapus Misi Satuan (Cloudinary Sync)
     */
    public function destroy($id) {
        DB::transaction(function () use ($id) {
            $mission = Mission::with('level', 'steps.hotspots')->findOrFail($id);
            $affectedUserIds = Progress::where('mission_id', $id)->pluck('user_id');
            
            $this->deleteMissionAssets($mission);
            
            $levelId = $mission->level_id;
            $mission->delete();
            if ($levelId) Level::destroy($levelId);
            foreach ($affectedUserIds as $userId) { $this->recalculateUserTotalXp($userId); }
        });
        return back()->with('success', 'Misi dan aset Cloudinary dihapus.');
    }

    /**
     * (View) Tampilkan Storyboard Langkah Misi
     */
    public function showSteps($id) {
        $mission = Mission::with(['steps' => fn($q) => $q->orderBy('step_order', 'asc')])->findOrFail($id);
        return view('admin.missions.steps', compact('mission'));
    }

    /**
     * (Action) Simpan Langkah Misi Baru (Upload Cloudinary)
     */
    public function storeStep(Request $request, $id) {
        $request->validate(['image' => 'required|image|max:2048', 'instruction' => 'required']);
        
        $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
        $upload = $uploadApi->upload($request->file('image')->getRealPath(), ['folder' => 'mission_steps']);

        MissionStep::create([
            'mission_id' => $id, 
            'step_image' => $upload['secure_url'], 
            'instruction' => $request->instruction,
            'key_answer_cell' => $request->key_answer_cell ?? '-', 
            'step_order' => MissionStep::where('mission_id', $id)->count() + 1,
            'target_x' => 0, 'target_y' => 0,
        ]);
        return back()->with('success', 'Langkah berhasil diunggah ke awan.');
    }

    /**
     * (Action) Hapus Langkah Misi (Clean Cloudinary)
     */
    public function destroyStep($id) {
        $step = MissionStep::findOrFail($id);
        if ($step->step_image) {
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
            $uploadApi->destroy($this->getPublicId($step->step_image));
        }
        $step->delete();
        return back()->with('success', 'Langkah dan gambar di awan dihapus.');
    }

    public function reorderSteps(Request $request) {
        foreach ($request->order as $index => $id) {
            MissionStep::where('id', $id)->update(['step_order' => $index + 1]);
        }
        return response()->json(['status' => 'success']);
    }

    public function reorderLevels(Request $request) {
        try {
            DB::transaction(function () use ($request) {
                foreach ($request->order as $index => $missionId) {
                    $m = Mission::find($missionId);
                    if ($m && $m->level_id) {
                        $newPos = $index + 1;
                        DB::table('levels')->where('id', $m->level_id)->update([
                            'level_order' => $newPos, 
                            'level_name' => 'Level ' . $newPos
                        ]);
                    }
                }
            });
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) { return response()->json(['status' => 'error'], 500); }
    }

    /**
     * (Action) Update Konten Soal & Gambar Utama Misi (Cloudinary Sync)
     */
    public function updateContent(Request $request, $id) {
        $mission = Mission::findOrFail($id);
        $data = $request->validate([
            'question' => 'required', 'key_answer' => 'required', 
            'distractors' => 'nullable', 'mission_image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('mission_image')) {
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
            
            if ($mission->mission_image) {
                $uploadApi->destroy($this->getPublicId($mission->mission_image));
            }
            
            $upload = $uploadApi->upload($request->file('mission_image')->getRealPath(), ['folder' => 'missions']);
            
            $data['mission_image'] = $upload['secure_url']; 
        }

        $mission->update($data);
        return back()->with('success', 'Konten misi diperbarui.');
    }

    public function builder($stepId) {
        $step = MissionStep::with('hotspots')->findOrFail($stepId);
        $mission = Mission::findOrFail($step->mission_id);
        return view('admin.missions.builder', compact('step', 'mission'));
    }

    /**
     * (Action) Plotting Hotspot & Upload Video (Anti-Nguwer)
     */
    public function storeHotspot(Request $request) {
        $request->validate([
            'step_id' => 'required', 
            'x_percent' => 'required', 
            'y_percent' => 'required', 
            'content' => 'required',
            'video' => 'nullable|mimes:mp4,mov,avi|max:10240' // Diperketat 10MB biar gak timeout
        ]);
        
        $videoPath = null;
        if ($request->hasFile('video')) {
            set_time_limit(300);
            $config = Configuration::instance(env('CLOUDINARY_URL'));
            $config->api->uploadTimeout = 300;
            
            $uploadApi = new UploadApi($config);
            $upload = $uploadApi->upload($request->file('video')->getRealPath(), [
                'folder'        => 'hotspot_videos',
                'resource_type' => 'video',
                'chunk_size'    => 6000000
            ]);
            $videoPath = $upload['secure_url'];
        }

        MissionHotspot::create([
            'step_id'   => $request->step_id, 
            'x_percent' => $request->x_percent, 
            'y_percent' => $request->y_percent, 
            'content'   => $request->content, 
            'video_path'=> $videoPath,
            'order'     => MissionHotspot::where('step_id', $request->step_id)->count() + 1
        ]);
        return back()->with('success', 'Titik target berhasil dipetakan.');
    }

    /**
     * (Action) Hapus Hotspot (Hapus Video di Awan)
     */
    public function destroyHotspot($id) { 
        $hs = MissionHotspot::findOrFail($id);
        if ($hs->video_path) {
            $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));
            $uploadApi->destroy($this->getPublicId($hs->video_path), ['resource_type' => 'video']);
        }
        $hs->delete(); 
        return back()->with('success', 'Titik berhasil dihapus.'); 
    }

    public function reorderHotspots(Request $request) {
        DB::transaction(function () use ($request) {
            foreach ($request->order as $index => $id) {
                MissionHotspot::where('id', $id)->update(['order' => $index + 1]);
            }
        });
        return response()->json(['status' => 'success']);
    }

    // --- LOGIKA HELPER ---

    private function syncUserXpForMission($missionId) {
        $mission = Mission::findOrFail($missionId);
        Progress::where('mission_id', $missionId)->where('score', '>', $mission->max_score)->update(['score' => $mission->max_score]);
        $userIds = Progress::where('mission_id', $missionId)->pluck('user_id');
        foreach ($userIds as $userId) { $this->recalculateUserTotalXp($userId); }
    }

    private function recalculateUserTotalXp($userId) {
        $totalXp = Progress::where('user_id', $userId)->where('status', 'completed')->sum('score');
        ScoresAndRanking::updateOrCreate(['user_id' => $userId], ['total_xp' => $totalXp]);
    }

    private function deleteMissionAssets($mission) {
        $uploadApi = new UploadApi(Configuration::instance(env('CLOUDINARY_URL')));

        if ($mission->mission_image) {
            $uploadApi->destroy($this->getPublicId($mission->mission_image));
        }

        foreach ($mission->steps as $step) {
            if ($step->step_image) {
                $uploadApi->destroy($this->getPublicId($step->step_image));
            }
            foreach ($step->hotspots as $hs) { 
                if ($hs->video_path) {
                    $uploadApi->destroy($this->getPublicId($hs->video_path), ['resource_type' => 'video']); 
                }
            }
        } 
    }

    private function getPublicId($url) {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', $path);
        $filename = end($segments);
        $folder = $segments[count($segments)-2];
        return $folder . '/' . pathinfo($filename, PATHINFO_FILENAME);
    }
}