<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\Admin;

class ProfilController extends Controller
{
    /**
     * Menampilkan profil admin yang sedang login.
     */
    public function index()
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $admin->load('unit');

        return view('admin.profil', compact('admin'));
    }

    /**
     * Memperbarui profil admin.
     */
    public function update(Request $request)
    {
        
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'nama_lengkap_gelar' => [
                'required',
                'string',
                'max:150',
            ],
            'nomor_telepon' => [
                'nullable',
                'string',
                'max:30',
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        if ($request->hasFile('avatar')) {
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }

            $validated['avatar'] = $request
                ->file('avatar')
                ->store('avatar-admin', 'public');
        }

        $admin->nama_lengkap_gelar =
            $validated['nama_lengkap_gelar'];

        $admin->nomor_telepon =
            $validated['nomor_telepon'] ?? null;

        if (isset($validated['avatar'])) {
            $admin->avatar = $validated['avatar'];
        }

        $admin->diperbarui_pada = now();

        $admin->save();

        return redirect()
            ->route('admin.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Mengganti kata sandi admin.
     */
    public function updatePassword(Request $request)
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'kata_sandi_saat_ini' => [
                'required',
                'string',
            ],
            'kata_sandi_baru' => [
                'required',
                'string',
                'confirmed',
                'different:kata_sandi_saat_ini',
                Password::min(8),
            ],
        ], [
            'kata_sandi_saat_ini.required' =>
                'Kata sandi saat ini wajib diisi.',
            'kata_sandi_baru.required' =>
                'Kata sandi baru wajib diisi.',
            'kata_sandi_baru.confirmed' =>
                'Konfirmasi kata sandi baru tidak cocok.',
            'kata_sandi_baru.different' =>
                'Kata sandi baru harus berbeda dari kata sandi saat ini.',
            'kata_sandi_baru.min' =>
                'Kata sandi baru minimal 8 karakter.',
        ]);

        if (! Hash::check(
            $validated['kata_sandi_saat_ini'],
            $admin->kata_sandi
        )) {
            return back()
                ->withErrors([
                    'kata_sandi_saat_ini' =>
                        'Kata sandi saat ini tidak sesuai.',
                ])
                ->withInput();
        }

        $admin->kata_sandi = Hash::make(
            $validated['kata_sandi_baru']
        );

        $admin->diperbarui_pada = now();

        $admin->save();

        return redirect()
            ->route('admin.profil')
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
