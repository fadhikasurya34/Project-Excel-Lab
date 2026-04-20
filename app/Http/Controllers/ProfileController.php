<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** (View) Menampilkan form profil dengan perbedaan tampilan antara Admin dan Siswa */
    public function edit(Request $request): View
    {
        if ($request->user()->role === 'admin') {
            return view('profile.admin', [
                'user' => $request->user(),
            ]);
        }

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /** (Action) Memperbarui informasi profil dan menangani validasi ulang email jika berubah */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /** (Action) Menghapus akun user secara permanen dan membersihkan sesi aktif */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi: Memastikan user memasukkan password yang benar sebelum hapus permanen
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        // Logika Keamanan: Invalidate session agar tidak bisa diakses kembali setelah logout
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}