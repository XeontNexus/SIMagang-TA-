@extends('layouts.app')

@section('title', 'Laporan Presensi - SIMagang')
@section('page-title', 'Laporan Presensi')

@section('content')
<div class="alert alert-info py-2 small mb-3">
    <i class="fas fa-info-circle me-1"></i>
    Data presensi ditampilkan untuk <strong>7 hari terakhir</strong>
    ({{ $startDate->translatedFormat('d F Y') }} — {{ $endDate->translatedFormat('d F Y') }}).
    Data lebih lama dihapus otomatis oleh sistem.
</div>

{{-- Filter --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Cari Siswa</label>
                <input type="text" name="search" class="form-control" placeholder="Nama / username / institusi..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       min="{{ $startDate->format('Y-m-d') }}"
                       max="{{ $endDate->format('Y-m-d') }}"
                       value="{{ $selectedDate->format('Y-m-d') }}">
                <small class="text-muted">Default: hari ini. Bisa lihat maks 7 hari ke belakang.</small>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('admin.presensi.report') }}" class="btn btn-secondary">
                    <i class="fas fa-sync-alt me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tanggal yang ditampilkan --}}
<div class="d-flex align-items-center gap-3 mb-3">
    <h5 class="mb-0">
        <i class="fas fa-calendar-day me-2 text-primary"></i>
        Presensi: <strong class="text-primary">{{ $selectedDate->translatedFormat('l, d F Y') }}</strong>
    </h5>
    @if($selectedDate->isToday())
        <span class="badge bg-success">Hari Ini</span>
    @endif
</div>

{{-- Statistik Ringkas --}}
@php
    $countHadir      = $presensiReport->filter(fn($r) => $r['type'] === 'presensi' && $r['presensi']->status === 'hadir')->count();
    $countIzin       = $presensiReport->filter(fn($r) => $r['type'] === 'presensi' && $r['presensi']->status === 'izin')->count();
    $countSakit      = $presensiReport->filter(fn($r) => $r['type'] === 'presensi' && $r['presensi']->status === 'sakit')->count();
    $countAlfa       = $presensiReport->filter(fn($r) => $r['type'] === 'presensi' && $r['presensi']->status === 'alfa')->count();
    $countBelum      = $presensiReport->filter(fn($r) => $r['type'] === 'belum_presensi')->count();
    $totalSiswa      = $presensiReport->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card border-0 bg-light text-center py-2">
            <div class="fs-4 fw-bold text-dark">{{ $totalSiswa }}</div>
            <div class="small text-muted">Total Siswa</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 bg-success bg-opacity-10 text-center py-2">
            <div class="fs-4 fw-bold text-success">{{ $countHadir }}</div>
            <div class="small text-muted">Hadir</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 bg-warning bg-opacity-10 text-center py-2">
            <div class="fs-4 fw-bold text-warning">{{ $countIzin }}</div>
            <div class="small text-muted">Izin</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 bg-info bg-opacity-10 text-center py-2">
            <div class="fs-4 fw-bold text-info">{{ $countSakit }}</div>
            <div class="small text-muted">Sakit</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 bg-danger bg-opacity-10 text-center py-2">
            <div class="fs-4 fw-bold text-danger">{{ $countAlfa }}</div>
            <div class="small text-muted">Alfa</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 bg-secondary bg-opacity-10 text-center py-2">
            <div class="fs-4 fw-bold text-secondary">{{ $countBelum }}</div>
            <div class="small text-muted">Belum Presensi</div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-week me-2"></i>Rekap Presensi
        </h6>
        <span class="badge bg-primary px-3 py-2">{{ $totalSiswa }} Siswa</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light presensi-report-thead">
                    <tr>
                        <th width="4%" class="text-center">No</th>
                        <th width="20%" class="text-start">Nama Siswa</th>
                        <th width="13%" class="text-start">Tempat Mitra</th>
                        <th width="8%" class="text-center">Jam Masuk</th>
                        <th width="8%" class="text-center">Jam Keluar</th>
                        <th width="13%" class="text-center">Koordinat</th>
                        <th width="8%" class="text-center">Kecocokan</th>
                        <th width="13%" class="text-center">Status</th>
                        <th width="7%" class="text-center">Bukti</th>
                        <th width="6%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensiReport as $index => $row)
                        @php
                            $user    = $row['user'];
                            $presensi = $row['presensi'];
                            $isBelum = $row['type'] === 'belum_presensi';
                            $kecocokanData = (!$isBelum && $presensi) ? $presensi->calculateKecocokan() : null;
                        @endphp
                        <tr class="{{ $isBelum ? 'table-secondary' : '' }}">
                            <td class="text-center">{{ $index + 1 }}</td>

                            {{-- Nama Siswa --}}
                            <td>
                                <strong>{{ $user->nama_lengkap }}</strong>
                                <div class="small text-muted">
                                    {{ $user->kelas?->tingkat }}-{{ $user->kelas?->nama_kelas }}
                                    ({{ $user->jurusan?->nama_jurusan }})
                                </div>
                            </td>

                            {{-- Mitra --}}
                            <td>
                                <span class="fw-semibold text-secondary small">
                                    <i class="fas fa-building me-1 text-muted"></i>
                                    {{ $user->mitra?->nama_mitra ?? $user->mitra_magang ?? '-' }}
                                </span>
                            </td>

                            {{-- Jam Masuk --}}
                            <td class="text-center">
                                @if(!$isBelum && $presensi->jam_masuk)
                                    <span class="fw-bold text-dark">
                                        <i class="far fa-clock me-1 text-success"></i>
                                        {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Jam Keluar --}}
                            <td class="text-center">
                                @if(!$isBelum && $presensi->jam_keluar)
                                    <span class="fw-bold text-dark">
                                        <i class="far fa-clock me-1 text-danger"></i>
                                        {{ \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Koordinat --}}
                            <td class="text-center">
                                @if(!$isBelum && $presensi->status === 'hadir' && $presensi->latitude_masuk && $presensi->longitude_masuk)
                                    <a href="https://www.google.com/maps?q={{ $presensi->latitude_masuk }},{{ $presensi->longitude_masuk }}"
                                       target="_blank" class="btn btn-sm btn-outline-danger w-100 text-start">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ round($presensi->latitude_masuk, 4) }}, {{ round($presensi->longitude_masuk, 4) }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Kecocokan --}}
                            <td class="text-center">
                                @if($kecocokanData)
                                    @php
                                        $pct = $kecocokanData['percentage'];
                                        $badgeColor = $pct < 70 ? 'danger' : ($pct < 90 ? 'warning' : 'success');
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }} fs-6 py-2 px-3">{{ $pct }}%</span>
                                    <div class="small text-muted mt-1" style="font-size:0.72rem;">
                                        {{ \App\Helpers\LocationHelper::formatDistance($kecocokanData['distance']) }}
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($isBelum)
                                    <span class="badge bg-secondary px-2 py-1">
                                        <i class="fas fa-clock me-1"></i>Belum Presensi
                                    </span>
                                @else
                                    @php
                                        $statusColor = match($presensi->status) {
                                            'hadir' => 'success',
                                            'izin'  => 'warning',
                                            'sakit' => 'info',
                                            'alfa'  => 'danger',
                                            default => 'secondary'
                                        };
                                        $statusIcon = match($presensi->status) {
                                            'hadir' => 'fa-check-circle',
                                            'izin'  => 'fa-clipboard',
                                            'sakit' => 'fa-thermometer-half',
                                            'alfa'  => 'fa-times-circle',
                                            default => 'fa-question-circle'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} px-2 py-1">
                                        <i class="fas {{ $statusIcon }} me-1"></i>{{ ucfirst($presensi->status) }}
                                    </span>
                                    @if($presensi->keterangan)
                                        <div class="small text-secondary mt-1" style="font-size:0.72rem;">{{ $presensi->keterangan }}</div>
                                    @endif
                                @endif
                            </td>

                            {{-- Bukti Foto --}}
                            <td class="text-center">
                                @if(!$isBelum && $presensi->hasBuktiFoto())
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" data-bs-target="#buktiModal"
                                            data-nama="{{ $user->nama_lengkap }}"
                                            data-tanggal="{{ $presensi->tanggal->translatedFormat('d F Y') }}"
                                            data-status="{{ ucfirst($presensi->status) }}"
                                            data-keterangan="{{ $presensi->keterangan ?? '-' }}"
                                            data-foto="{{ $presensi->buktiFotoUrl() }}"
                                            data-download="{{ route('admin.presensi.bukti.download', $presensi) }}"
                                            data-detail="{{ route('admin.presensi.bukti', $presensi) }}">
                                        <i class="fas fa-image"></i>
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                @if($isBelum)
                                    {{-- Belum presensi: tombol Tambah --}}
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                            title="Tambah Presensi"
                                            data-bs-toggle="modal" data-bs-target="#tambahPresensiModal"
                                            data-user-id="{{ $user->id }}"
                                            data-nama="{{ $user->nama_lengkap }}"
                                            data-tanggal="{{ $row['tanggal']->format('Y-m-d') }}"
                                            data-tanggal-label="{{ $row['tanggal']->translatedFormat('d F Y') }}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                @else
                                    {{-- Sudah presensi: tombol Edit --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                            title="Edit Status Presensi"
                                            data-bs-toggle="modal" data-bs-target="#editStatusModal"
                                            data-presensi-id="{{ $presensi->id }}"
                                            data-nama="{{ $user->nama_lengkap }}"
                                            data-tanggal="{{ $presensi->tanggal->translatedFormat('d F Y') }}"
                                            data-status="{{ $presensi->status }}"
                                            data-keterangan="{{ $presensi->keterangan ?? '' }}"
                                            data-jam-masuk="{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '' }}"
                                            data-jam-keluar="{{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '' }}"
                                            data-update-url="{{ route('admin.presensi.update-status', $presensi) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-2 d-block text-secondary opacity-50"></i>
                                Tidak ada siswa terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Tambah Presensi (untuk siswa yang belum presensi) --}}
<div class="modal fade" id="tambahPresensiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Presensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tambahPresensiForm" method="POST" action="{{ route('admin.presensi.store') }}">
                @csrf
                <input type="hidden" name="user_id" id="tambahUserId">
                <input type="hidden" name="tanggal" id="tambahTanggal">
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="fw-bold text-primary" id="tambahNamaSiswa"></div>
                        <small class="text-muted" id="tambahTanggalLabel"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" id="tambahStatus" class="form-select" required>
                            <option value="hadir">✅ Hadir</option>
                            <option value="izin">📋 Izin</option>
                            <option value="sakit">🤒 Sakit</option>
                            <option value="alfa">❌ Alfa</option>
                        </select>
                    </div>
                    <div id="tambahJamGroup" class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Jam Masuk</label>
                            <input type="time" name="jam_masuk" id="tambahJamMasuk" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Jam Keluar</label>
                            <input type="time" name="jam_keluar" id="tambahJamKeluar" class="form-control">
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Opsional — isi jika perlu mencatat jam</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan <span class="text-muted">(opsional)</span></label>
                        <input type="text" name="keterangan" id="tambahKeterangan" class="form-control"
                               placeholder="Contoh: Ditambahkan oleh admin">
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Presensi ini ditambahkan secara manual oleh admin.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Simpan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit Status Presensi --}}
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark"><i class="fas fa-edit me-2"></i>Edit Status Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStatusForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="fw-bold text-primary" id="editNamaSiswa"></div>
                        <small class="text-muted" id="editTanggal"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" id="editStatus" class="form-select" required>
                            <option value="hadir">✅ Hadir</option>
                            <option value="izin">📋 Izin</option>
                            <option value="sakit">🤒 Sakit</option>
                            <option value="alfa">❌ Alfa</option>
                        </select>
                    </div>
                    <div id="jamGroup" class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Jam Masuk</label>
                            <input type="time" name="jam_masuk" id="editJamMasuk" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Jam Keluar</label>
                            <input type="time" name="jam_keluar" id="editJamKeluar" class="form-control">
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Opsional — isi jika perlu menyesuaikan jam presensi</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan <span class="text-muted">(opsional)</span></label>
                        <input type="text" name="keterangan" id="editKeterangan" class="form-control"
                               placeholder="Contoh: Lupa presensi, dikoreksi admin">
                    </div>
                    <div class="alert alert-warning py-2 small mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Perubahan status ini dicatat sebagai koreksi admin. Pastikan sudah dikonfirmasi dengan siswa yang bersangkutan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Bukti Foto --}}
<div class="modal fade" id="buktiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-camera me-2"></i>Detail Bukti Presensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Nama Siswa:</strong> <span id="buktiNama">-</span></div>
                    <div class="col-md-6"><strong>Tanggal:</strong> <span id="buktiTanggal">-</span></div>
                    <div class="col-md-6 mt-2"><strong>Status:</strong> <span id="buktiStatus">-</span></div>
                    <div class="col-md-12 mt-2"><strong>Keterangan:</strong> <span id="buktiKeterangan">-</span></div>
                </div>
                <div class="text-center border rounded p-2 bg-light">
                    <img id="buktiFotoPreview" src="" alt="Bukti Foto" class="img-fluid rounded" style="max-height: 420px;">
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="buktiDownloadBtn" class="btn btn-success" download>
                    <i class="fas fa-download me-1"></i>Unduh Foto
                </a>
                <a href="#" id="buktiFullPageBtn" class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>Buka Halaman Penuh
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .presensi-report-thead th {
        vertical-align: middle;
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script>
// Modal: Bukti Foto
document.getElementById('buktiModal')?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('buktiNama').textContent    = button.getAttribute('data-nama') || '-';
    document.getElementById('buktiTanggal').textContent = button.getAttribute('data-tanggal') || '-';
    document.getElementById('buktiStatus').textContent  = button.getAttribute('data-status') || '-';
    document.getElementById('buktiKeterangan').textContent = button.getAttribute('data-keterangan') || '-';
    document.getElementById('buktiFotoPreview').src     = button.getAttribute('data-foto') || '';
    document.getElementById('buktiDownloadBtn').href    = button.getAttribute('data-download') || '#';
    document.getElementById('buktiFullPageBtn').href    = button.getAttribute('data-detail') || '#';
});

// Modal: Edit Status Presensi
document.getElementById('editStatusModal')?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const form = document.getElementById('editStatusForm');

    document.getElementById('editNamaSiswa').textContent = button.getAttribute('data-nama') || '-';
    document.getElementById('editTanggal').textContent   = 'Tanggal: ' + (button.getAttribute('data-tanggal') || '-');

    const statusSelect  = document.getElementById('editStatus');
    const currentStatus = button.getAttribute('data-status') || 'alfa';
    statusSelect.value  = currentStatus;

    document.getElementById('editKeterangan').value  = button.getAttribute('data-keterangan') || '';
    document.getElementById('editJamMasuk').value    = button.getAttribute('data-jam-masuk') || '';
    document.getElementById('editJamKeluar').value   = button.getAttribute('data-jam-keluar') || '';

    form.action = button.getAttribute('data-update-url');
    toggleJamGroup(currentStatus, 'jamGroup');
});

// Modal: Tambah Presensi
document.getElementById('tambahPresensiModal')?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('tambahUserId').value         = button.getAttribute('data-user-id');
    document.getElementById('tambahTanggal').value        = button.getAttribute('data-tanggal');
    document.getElementById('tambahNamaSiswa').textContent = button.getAttribute('data-nama') || '-';
    document.getElementById('tambahTanggalLabel').textContent = 'Tanggal: ' + (button.getAttribute('data-tanggal-label') || '-');
    document.getElementById('tambahStatus').value = 'hadir';
    document.getElementById('tambahJamMasuk').value  = '';
    document.getElementById('tambahJamKeluar').value = '';
    document.getElementById('tambahKeterangan').value = '';
    toggleJamGroup('hadir', 'tambahJamGroup');
});

function toggleJamGroup(status, groupId) {
    const group = document.getElementById(groupId);
    if (group) {
        group.style.display = (status === 'hadir') ? '' : 'none';
    }
}

document.getElementById('editStatus')?.addEventListener('change', function () {
    toggleJamGroup(this.value, 'jamGroup');
});
document.getElementById('tambahStatus')?.addEventListener('change', function () {
    toggleJamGroup(this.value, 'tambahJamGroup');
});
</script>
@endpush
@endsection
