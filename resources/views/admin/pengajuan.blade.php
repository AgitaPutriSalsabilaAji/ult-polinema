@extends('adminlte::page')

@section('title', 'Data Pengajuan')

@section('content_header')
    <h1 class="h4 font-weight-bold">Data Pengajuan Pelayanan</h1>
@stop

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.pengajuan.index') }}"
                  class="row">
                <div class="col-lg-5 mb-2">
                    <input type="search" name="q" class="form-control"
                           value="{{ request('q') }}"
                           placeholder="Cari ID, nama pemohon, atau subjek...">
                </div>

                <div class="col-lg-3 mb-2">
                    <select name="unit" class="form-control">
                        <option value="">Semua Unit Pelaksana</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}"
                                @selected((string)request('unit') === (string)$unit->id)>
                                {{ $unit->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        @foreach([
                            'diajukan' => 'Diajukan',
                            'baru' => 'Baru',
                            'diproses' => 'Diproses',
                            'didisposisikan' => 'Didisposisikan',
                            'selesai' => 'Selesai',
                            'ditolak' => 'Ditolak',
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                @selected(request('status') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 mb-2">
                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>ID Pengajuan</th>
                            <th>Nama Pemohon</th>
                            <th>Layanan Diminta</th>
                            <th>Unit</th>
                            <th>Tanggal Masuk</th>
                            <th>Status</th>
                            <th>Admin FO</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($pengajuans as $item)
                        @php
                            $statusMap = [
                                'diajukan' => ['DIAJUKAN', 'secondary'],
                                'baru' => ['BARU', 'primary'],
                                'diproses' => ['DIPROSES', 'warning'],
                                'didisposisikan' => ['DIDISPOSISIKAN', 'info'],
                                'selesai' => ['SELESAI', 'success'],
                                'ditolak' => ['DITOLAK', 'danger'],
                            ];

                            [$statusLabel, $statusColor] =
                                $statusMap[$item->status] ?? [
                                    strtoupper($item->status), 'secondary'
                                ];
                        @endphp
                        <tr>
                            <td>{{ $pengajuans->firstItem() + $loop->index }}</td>
                            <td>
                                <strong class="text-primary">
                                    {{ $item->nomor_tiket }}
                                </strong>
                                <div class="small text-muted">
                                    #{{ $item->id }}
                                </div>
                            </td>
                            <td>
                                <strong>{{ $item->nama_pemohon ?? 'Pemohon #' . $item->id_pengguna }}</strong>
                                @if(!empty($item->nip_pemohon))
                                    <div class="small text-muted">
                                        NIP: {{ $item->nip_pemohon }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $item->nama_layanan ?? $item->subjek }}</td>
                            <td>{{ $item->nama_unit ?? 'Belum ditentukan' }}</td>
                            <td>
                                {{ $item->dibuat_pada
                                    ? \Carbon\Carbon::parse($item->dibuat_pada)->format('d/m/Y')
                                    : '-' }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>{{ $item->nama_admin_fo ?? 'Belum ditugaskan' }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.pengajuan.show', $item->id) }}"
                                   class="btn btn-xs btn-outline-secondary">
                                    Detail
                                </a>

                                @if(!in_array($item->status, ['selesai', 'ditolak']))
                                    <button type="button"
                                            class="btn btn-xs btn-primary"
                                            data-toggle="modal"
                                            data-target="#modalDisposisi"
                                            data-id="{{ $item->id }}"
                                            data-tiket="{{ $item->nomor_tiket }}"
                                            data-pemohon="{{ $item->nama_pemohon ?? 'Pemohon #' . $item->id_pengguna }}"
                                            data-layanan="{{ $item->nama_layanan ?? $item->subjek }}"
                                            data-status="{{ $statusLabel }}">
                                        Disposisi
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-4">
                                Belum ada data pengajuan.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <small>
                Menampilkan {{ $pengajuans->firstItem() ?? 0 }}–{{ $pengajuans->lastItem() ?? 0 }}
                dari {{ $pengajuans->total() }} pengajuan
            </small>
            {{ $pengajuans->links() }}
        </div>
    </div>
</div>

{{-- MODAL DISPOSISI --}}
<div class="modal fade" id="modalDisposisi" tabindex="-1"
     aria-labelledby="judulModalDisposisi" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#102542">
                <div>
                    <h5 class="modal-title" id="judulModalDisposisi">
                        Disposisi Pengajuan ke Unit Kerja
                    </h5>
                    <small>Teruskan berkas permohonan ke admin pelaksana</small>
                </div>
                <button type="button" class="close text-white"
                        data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formDisposisi" method="POST" action="#">
                @csrf
                <div class="modal-body">
                    <div class="border rounded bg-light p-3 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">E-Ticket</small>
                                <div id="detailTiket" class="font-weight-bold"></div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Pemohon</small>
                                <div id="detailPemohon" class="font-weight-bold"></div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Layanan</small>
                                <div id="detailLayanan" class="font-weight-bold"></div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Status Saat Ini</small>
                                <div id="detailStatus"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_unit_tujuan">Unit Tujuan Disposisi *</label>
                        <select name="id_unit_tujuan" id="id_unit_tujuan"
                                class="form-control" required>
                            <option value="">Pilih Unit Tujuan</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_admin_tujuan">Admin Staf Tujuan *</label>
                        <select name="id_admin_tujuan" id="id_admin_tujuan"
                                class="form-control" required disabled>
                            <option value="">Pilih unit terlebih dahulu</option>
                            @foreach($adminTujuan as $admin)
                                <option value="{{ $admin->id }}"
                                        data-unit="{{ $admin->id_unit }}">
                                    {{ $admin->nama_lengkap_gelar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="catatan_disposisi">
                            Catatan Instruksi Disposisi (Opsional)
                        </label>
                        <textarea name="catatan_disposisi"
                                  id="catatan_disposisi"
                                  class="form-control" rows="3"
                                  maxlength="5000"
                                  placeholder="Tuliskan instruksi kepada unit tujuan..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Disposisi Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table th, .table td {
        vertical-align: middle;
        padding: 11px 9px;
        font-size: 12px;
    }
    .table thead th { white-space: nowrap; }
    .btn-xs { padding: 4px 7px; font-size: 11px; }
    .modal-content { border: 0; border-radius: 10px; overflow: hidden; }
</style>
@stop

@section('js')
<script>
$(function () {
    const baseUrl = @json(url('/admin/pengajuan'));

    $('#modalDisposisi').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');

        $('#detailTiket').text(button.data('tiket'));
        $('#detailPemohon').text(button.data('pemohon'));
        $('#detailLayanan').text(button.data('layanan'));
        $('#detailStatus').text(button.data('status'));

        $('#formDisposisi').attr(
            'action',
            baseUrl + '/' + encodeURIComponent(id) + '/disposisi'
        );

        $('#id_unit_tujuan').val('').trigger('change');
        $('#catatan_disposisi').val('');
    });

    $('#id_unit_tujuan').on('change', function () {
        const unitId = String($(this).val());
        const selectAdmin = $('#id_admin_tujuan');

        selectAdmin.empty();

        if (!unitId) {
            selectAdmin.append(
                new Option('Pilih unit terlebih dahulu', '')
            ).prop('disabled', true);
            return;
        }

        selectAdmin.append(new Option('Pilih Admin Tujuan', ''));

        let jumlahAdmin = 0;

        selectAdmin.find('option[data-unit]').each(function () {
            if (String($(this).data('unit')) === unitId) {
                jumlahAdmin++;
            }
        });

        // Opsi admin difilter dari daftar asli yang disimpan di bawah.
        const admins = {{ Illuminate\Support\Js::from(
            $adminTujuan->map(function ($a) {
                return [
                    'id' => $a->id,
                    'nama' => $a->nama_lengkap_gelar,
                    'id_unit' => $a->id_unit,
                ];
            })->values()
        ) }};

        admins.forEach(function (admin) {
            if (String(admin.unit) === unitId) {
                selectAdmin.append(
                    new Option(admin.nama, admin.id)
                );
            }
        });

        if (selectAdmin.children('option').length <= 1) {
            selectAdmin.empty().append(
                new Option('Belum ada admin untuk unit ini', '')
            ).prop('disabled', true);
        } else {
            selectAdmin.prop('disabled', false);
        }
    });
});
</script>
@stop
