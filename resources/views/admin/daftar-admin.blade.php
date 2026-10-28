@extends('adminlte::page')

@section('title', 'Daftar Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Daftar Admin</h4>
            <p class="text-muted mb-0">
                Kelola akun dan akses administrator ULT Polinema.
            </p>
        </div>

        <a href="{{ route('admin.daftar-admin.create') }}"
           class="btn btn-primary">
            + Tambah Admin
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            Periksa kembali data yang dimasukkan.
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold">Administrator terdaftar</h6>
            <p class="small text-muted">
                Kelola akun dan hak akses administrator ULT Polinema.
            </p>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama Lengkap & Gelar</th>
                            <th>Peran Pengguna</th>
                            <th>Unit Terdaftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($admins as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->nama_lengkap_gelar }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $item->email }}
                                    </small>
                                </td>
                                <td>
                                    @if ($item->peran === 'admin_ult')
                                        <span class="badge bg-primary">
                                            Admin ULT (Super Access)
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">
                                            Admin Unit
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->unit?->nama_unit ?? '-' }}
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.daftar-admin.edit', $item) }}"
                                       class="btn btn-sm btn-link">
                                        Edit
                                    </a>

                                    @if ($item->id !== auth('admin')->id())
                                        <form
                                            action="{{ route('admin.daftar-admin.destroy', $item) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus akun admin ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-link text-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data admin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $admins->links() }}
            </div>
        </div>
    </div>
</div>
@endsection