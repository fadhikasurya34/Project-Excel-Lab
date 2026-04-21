<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\MisiController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PeringkatController;
use App\Http\Controllers\Admin\AdminMateriController;
use App\Http\Controllers\Admin\AdminMissionController;
use App\Http\Controllers\Admin\AdminClassroomController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes - Virtual Lab Excel
|--------------------------------------------------------------------------
*/
// --- 1. PUBLIC & AUTHENTICATION ---
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('auth.login');
});

// Dashboard Utama: Distribusi Role (Admin vs Siswa)
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Manajemen Profil (Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// --- 2. STUDENT / SISWA ROUTES ---
Route::middleware(['auth'])->group(function () {
    
    // Eksplorasi Materi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/materi/{id}', [MateriController::class, 'show'])->name('materi.show');
    
    // Petualangan Misi
    Route::get('/misi', [MisiController::class, 'index'])->name('misi.index'); 
    Route::get('/misi/materi/{category}', [MisiController::class, 'showLevels'])->name('misi.category.levels'); 
    Route::get('/misi/level/{id}', [MisiController::class, 'show'])->name('misi.show'); 
    Route::post('/misi/check/{id}', [MisiController::class, 'checkAnswer'])->name('misi.check');
    
    // Fitur Tiket Remedial (Ulangi Misi)
    Route::post('/misi/retry/{id}', [MisiController::class, 'retryMission'])->name('misi.retry');
    
    // Squad Kelas (Sisi Siswa)
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{id}', [KelasController::class, 'show'])->name('kelas.show');
    Route::post('/kelas/gabung', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/task/{id}', [KelasController::class, 'showTask'])->name('kelas.task.show');
    
    // Leaderboard / Hall of Fame
    Route::get('/peringkat', [PeringkatController::class, 'index'])->name('peringkat.index');
    Route::get('/peringkat/{type}/{id?}', [PeringkatController::class, 'show'])->name('peringkat.show');
});


// --- 3. ADMIN ROUTES (MANAGEMENT PANEL) ---
Route::middleware(['auth', 'checkRole:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Statistik
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // A. MANAJEMEN MATERI (AdminMateriController)
    Route::controller(AdminMateriController::class)->group(function () {
        Route::get('/materials', 'index')->name('materials.index');
        Route::get('/materials/create', 'create')->name('materials.create');
        Route::post('/materials', 'store')->name('materials.store');
        Route::get('/materials/{id}/edit', 'edit')->name('materials.edit');
        Route::patch('/materials/{id}', 'update')->name('materials.update');
        Route::delete('/materials/{id}', 'destroy')->name('materials.destroy');
        
        // Step Management (Storyboard)
        Route::get('/materials/{id}/steps', 'showSteps')->name('materials.steps');
        Route::post('/materials/{id}/steps', 'storeStep')->name('materials.store-step');
        Route::delete('/materials/steps/{id}', 'destroyStep')->name('materials.steps.destroy');
        Route::post('/materials/steps/reorder', 'reorderSteps')->name('materials.reorder-steps');

        // Hotspot Management (Visual Builder)
        Route::get('/materials/steps/{stepId}/builder', 'builder')->name('materials.builder');
        Route::post('/materials/hotspots/store', 'storeHotspot')->name('materials.store-hotspot');
        Route::post('/materials/hotspots/reorder', 'reorderHotspots')->name('materials.reorder-hotspots');
        Route::delete('/materials/hotspots/{id}', 'destroyHotspot')->name('materials.destroy-hotspot');
    });

    // B. MANAJEMEN MISI (AdminMissionController)
    Route::controller(AdminMissionController::class)->group(function () {
        Route::get('/missions', 'index')->name('missions.index');
        Route::get('/missions/topic/{category}', 'listByTopic')->name('missions.topic');
        Route::patch('/missions/topic/{old_category}/update', 'updateTopic')->name('missions.update-topic');
        Route::delete('/missions/topic/{category}/destroy', 'destroyTopic')->name('missions.destroy-topic');

        Route::get('/missions/create', 'create')->name('missions.create');
        Route::post('/missions/create', 'store')->name('missions.store-step1');
        
        // --- TAMBAHAN BARU UNTUK WIZARD FLOW ---
        Route::get('/missions/create-detail/{level_id}', 'createDetail')->name('missions.create-detail');
        Route::post('/missions/store-detail', 'storeDetail')->name('missions.store-detail');
        // ----------------------------------------

        Route::post('/missions/store-quick', 'storeQuick')->name('missions.store-quick');
        Route::get('/missions/{id}/edit', 'edit')->name('missions.edit');
        Route::patch('/missions/{id}', 'update')->name('missions.update');
        Route::delete('/missions/{id}', 'destroy')->name('missions.destroy');
        Route::post('/missions/reorder-levels', 'reorderLevels')->name('missions.reorder-levels');

        Route::patch('/missions/{id}/content', 'updateContent')->name('missions.update-content');
        
        // Step Management (Storyboard & Reorder)
        Route::get('/missions/{id}/steps', 'showSteps')->name('missions.steps');
        Route::post('/missions/{id}/steps', 'storeStep')->name('missions.store-step');
        Route::delete('/missions/steps/{id}', 'destroyStep')->name('missions.destroy-step');
        Route::post('/missions/steps/reorder', 'reorderSteps')->name('missions.reorder-steps'); 
        
        // Visual Builder Misi
        Route::get('/missions/steps/{stepId}/builder', 'builder')->name('missions.builder');
        Route::post('/missions/hotspots', 'storeHotspot')->name('missions.store-hotspot');
        Route::post('/missions/hotspots/reorder', 'reorderHotspots')->name('missions.reorder-hotspots');
        Route::delete('/missions/hotspots/{id}', 'destroyHotspot')->name('missions.destroy-hotspot');
    });

    // C. MANAJEMEN KELAS (AdminClassroomController)
    Route::controller(AdminClassroomController::class)->group(function () {
        Route::get('/classrooms', 'index')->name('classrooms.index');
        Route::post('/classrooms', 'store')->name('classrooms.store');
        Route::put('/classrooms/{id}', 'update')->name('classrooms.update');
        Route::delete('/classrooms/{id}', 'destroy')->name('classrooms.destroy');
        Route::get('/classrooms/{id}', 'show')->name('classrooms.show');
        Route::delete('/classrooms/{id}/kick/{userId}', 'kick')->name('classrooms.kick');
        Route::post('/classrooms/{id}/tasks', 'storeTask')->name('classrooms.store-task');
        Route::put('/tasks/{id}', 'updateTask')->name('tasks.update');
        Route::delete('/tasks/{id}', 'destroyTask')->name('tasks.destroy');
        Route::get('/tasks/{id}/export', 'exportTask')->name('tasks.export');
    });

    // D. MANAJEMEN SISWA (AdminUserController)
    Route::controller(AdminUserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::get('/users/{id}', 'show')->name('users.show');
        Route::post('/users/{id}/reset-xp', 'resetXP')->name('users.reset-xp');
        
        // //* NEW: Rute untuk mereset tiket remedial harian siswa
        Route::post('/users/{id}/reset-tickets', 'resetRetryTickets')->name('users.reset-tickets');
        
        Route::delete('/users/{id}', 'destroy')->name('users.destroy');
        
        // Rute hapus pengerjaan MISI
        Route::delete('/users/progress/{id}', 'destroyMissionProgress')->name('users.destroy-progress');
        
        // Rute hapus pengerjaan MATERI
        Route::delete('/users/material-progress/{id}', 'destroyMaterialProgress')->name('users.destroy-material-progress');
    });
    
});

require __DIR__.'/auth.php';