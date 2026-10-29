@extends('adminlte::page')

@section('title', 'Daftar Admin')

@section('content')
<div class="container-fluid py-4 admin-list-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Daftar Admin</h4>
            <p class="text-muted mb-0">Kelola akun dan akses administrator ULT Polinema.</p>
        </div>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#addAdminModal" id="openAddAdmin">
            + Tambah Admin
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            Periksa kembali data yang dimasukkan.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold">Administrator terdaftar</h6>
            <p class="small text-muted">Kelola akun dan hak akses administrator ULT Polinema.</p>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama Lengkap &amp; Gelar</th>
                            <th>Peran Pengguna</th>
                            <th>Unit Terdaftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($admins as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->nama_lengkap_gelar }}</div>
                                    <small class="text-muted">{{ $item->email }}</small>
                                </td>
                                <td>
                                    @if ($item->peran === 'admin_ult')
                                        <span class="badge bg-primary">Admin ULT (Super Access)</span>
                                    @else
                                        <span class="badge bg-light text-dark">Admin Unit</span>
                                    @endif
                                </td>
                                <td>{{ $item->unit?->nama_unit ?? '-' }}</td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-link edit-admin"
                                            data-bs-toggle="modal" data-bs-target="#editAdminModal"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->nama_lengkap_gelar }}"
                                            data-email="{{ $item->email }}"
                                            data-phone="{{ $item->nomor_telepon }}"
                                            data-nip="{{ $item->nip }}"
                                            data-role="{{ $item->peran }}"
                                            data-unit="{{ $item->id_unit }}">
                                        Edit
                                    </button>

                                    @if ($item->id !== auth('admin')->id())
                                        <button type="button" class="btn btn-sm btn-link text-danger delete-admin"
                                                data-bs-toggle="modal" data-bs-target="#deleteAdminModal"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->nama_lengkap_gelar }}">
                                            Hapus
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada data admin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $admins->links() }}</div>
        </div>
    </div>
</div>

<div class="modal fade admin-modal" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="addAdminModalLabel">Tambah Admin</h5>
                    <p class="modal-subtitle mb-0">Tambahkan akun administrator baru ke sistem ULT Polinema.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form method="POST" action="{{ route('admin.daftar-admin.store') }}">
                @csrf
                <input type="hidden" name="_form" value="add">
                <div class="modal-body">
                    @include('admin.partials.admin-fields', ['admin' => null, 'form' => 'add'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary modal-submit">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade admin-modal" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="editAdminModalLabel">Edit Data Admin</h5>
                    <p class="modal-subtitle mb-0">Edit informasi administrator ke sistem ULT Polinema.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form method="POST" id="editAdminForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="edit">
                <input type="hidden" name="_admin_id" id="edit_admin_id">
                <div class="modal-body">
                    @include('admin.partials.admin-fields', ['admin' => null, 'form' => 'edit'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary modal-submit">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade admin-modal admin-delete-modal" id="deleteAdminModal" tabindex="-1"
     aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAdminModalLabel">Hapus Data?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form method="POST" id="deleteAdminForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus data admin <strong id="deleteAdminName"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger modal-submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .admin-list-page .table thead th { font-size: .72rem; text-transform: none; }
    .admin-modal .modal-content { border: 0; border-radius: .55rem; box-shadow: 0 1rem 3rem rgba(0,0,0,.2); }
    .admin-modal .modal-header { padding: 1.25rem 1.5rem .75rem; border-bottom: 0; }
    .admin-modal .modal-body { padding: .5rem 1.5rem 1rem; }
    .admin-modal .modal-footer { padding: .75rem 1.5rem 1.25rem; border-top: 0; }
    .admin-modal .modal-title { font-size: 1rem; font-weight: 700; }
    .modal-subtitle { color: #8b95a7; font-size: .7rem; }
    .admin-modal .form-label { font-size: .68rem; font-weight: 600; margin-bottom: .35rem; }
    .admin-modal .form-control, .admin-modal .form-select { min-height: 34px; font-size: .72rem; border-color: #e4e9f0; border-radius: .3rem; }
    .admin-modal .form-text { font-size: .62rem; color: #8b95a7; }
    .admin-modal .btn { font-size: .72rem; border-radius: .3rem; padding: .4rem .8rem; }
    .admin-delete-modal .modal-body { font-size: .75rem; color: #667085; }
    @media (max-width: 575.98px) { .admin-modal .modal-dialog { margin: .75rem; } }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editForm = document.getElementById('editAdminForm');
    const deleteForm = document.getElementById('deleteAdminForm');
    const routeTemplate = @json(url('/admin/daftar-admin'));
    const field = (form, name) => form.querySelector('[name="' + name + '"]');

    document.querySelectorAll('.edit-admin').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            editForm.action = routeTemplate + '/' + id;
            document.getElementById('edit_admin_id').value = id;
            field(editForm, 'nama_lengkap_gelar').value = this.dataset.name;
            field(editForm, 'email').value = this.dataset.email;
            field(editForm, 'nomor_telepon').value = this.dataset.phone || '';
            field(editForm, 'nip').value = this.dataset.nip;
            field(editForm, 'peran').value = this.dataset.role;
            field(editForm, 'id_unit').value = this.dataset.unit;
            field(editForm, 'kata_sandi').value = '';
        });
    });

    document.querySelectorAll('.delete-admin').forEach(function (button) {
        button.addEventListener('click', function () {
            deleteForm.action = routeTemplate + '/' + this.dataset.id;
            document.getElementById('deleteAdminName').textContent = this.dataset.name;
        });
    });

    document.querySelectorAll('.modal form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const submit = form.querySelector('.modal-submit');
            if (submit) {
                submit.disabled = true;
                submit.textContent = 'Memproses...';
            }
        });
    });

    @if ($errors->any() && old('_form') === 'edit' && old('_admin_id'))
        const failedEdit = document.querySelector('.edit-admin[data-id="{{ old('_admin_id') }}"]');
        if (failedEdit) {
            failedEdit.click();
            const failedValues = @json(old());
            Object.keys(failedValues).forEach(function (name) {
                const input = field(editForm, name);
                if (input && name !== '_token' && name !== '_method') {
                    input.value = failedValues[name];
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editAdminModal')).show();
        }
    @elseif ($errors->any() && old('_form') === 'add')
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addAdminModal')).show();
    @endif
});
</script>
@endpush
