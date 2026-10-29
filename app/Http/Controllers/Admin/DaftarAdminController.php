<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class DaftarAdminController extends Controller
{
    /**
     * Pastikan hanya Admin ULT yang dapat mengakses fitur ini.
     */
    private function authorizeUlt(): void
    {
        abort_unless(
            Auth::guard('admin')->user()?->peran === 'admin_ult',
            403,
            'Anda tidak memiliki izin mengakses halaman Daftar Admin.'
        );
    }

    public function index()
    {
        $this->authorizeUlt();

        $admins = Admin::with('unit')
            ->orderBy('id')
            ->paginate(10);
        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.daftar-admin', compact('admins', 'units'));
    }

    public function create()
    {
        $this->authorizeUlt();

        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.daftar-admin.form', [
            'admin' => new Admin(),
            'units' => $units,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeUlt();

        $validated = $request->validate([
            'nama_lengkap_gelar' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', 'unique:admin,email'],
            'nip' => ['required', 'string', 'max:50', 'unique:admin,nip'],
            'peran' => ['required', Rule::in(['admin_ult', 'admin_unit'])],
            'id_unit' => ['required', 'integer', 'exists:unit,id'],
            'nomor_telepon' => ['nullable', 'string', 'max:30'],
            'kata_sandi' => ['required', 'string', Password::min(8)],
        ]);

        $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        $validated['dibuat_pada'] = now();
        $validated['diperbarui_pada'] = now();

        Admin::create($validated);

        return redirect()
            ->route('admin.daftar-admin.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(Admin $daftar_admin)
    {
        $this->authorizeUlt();

        return view('admin.daftar-admin.form', [
            'admin' => $daftar_admin,
            'units' => Unit::orderBy('nama_unit')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Admin $daftar_admin)
    {
        $this->authorizeUlt();

        $validated = $request->validate([
            'nama_lengkap_gelar' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:100',
                Rule::unique('admin', 'email')->ignore($daftar_admin->id),
            ],
            'nip' => [
                'required', 'string', 'max:50',
                Rule::unique('admin', 'nip')->ignore($daftar_admin->id),
            ],
            'peran' => ['required', Rule::in(['admin_ult', 'admin_unit'])],
            'id_unit' => ['required', 'integer', 'exists:unit,id'],
            'nomor_telepon' => ['nullable', 'string', 'max:30'],
            'kata_sandi' => ['nullable', 'string', Password::min(8)],
        ]);

        if (!empty($validated['kata_sandi'])) {
            $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        } else {
            unset($validated['kata_sandi']);
        }

        $validated['diperbarui_pada'] = now();

        $daftar_admin->update($validated);

        return redirect()
            ->route('admin.daftar-admin.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy(Admin $daftar_admin)
    {
        $this->authorizeUlt();

        abort_if(
            $daftar_admin->id === Auth::guard('admin')->id(),
            403,
            'Anda tidak dapat menghapus akun yang sedang digunakan.'
        );

        $daftar_admin->delete();

        return redirect()
            ->route('admin.daftar-admin.index')
            ->with('success', 'Admin berhasil dihapus.');
    }
}