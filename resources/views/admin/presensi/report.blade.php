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

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Cari Siswa</label>
                <input type="text" name="search" class="form-control" placeholder="Nama / username / institusi..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Filter Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       min="{{ $startDate->format('Y-m-d') }}"
                       max="{{ $endDate->format('Y-m-d') }}"
                       value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('admin.presensi.report') }}" class="btn btn-secondary">
                    <i class="fas fa-sync-alt me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-week me-2"></i>Rekap Presensi (7 Hari Terakhir)
        </h6>
        <span class="badge bg-primary px-3 py-2">{{ $presensiReport->count() }} Data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light presensi-report-thead">
                    <tr>
                        <th width="4%" class="text-center align-middle">No</th>
                        <th width="10%" class="text-center align-middle">Tanggal</th>
                        <th width="17%" class="text-start align-middle">Nama Siswa</th>
                        <th width="13%" class="text-start align-middle">Tempat Mitra</th>
                        <th width="7%" class="text-center align-middle">Jam Masuk</th>
                        <th width="7%" class="text-center align-middle">Jam Keluar</th>
                        <th width="13%" class="text-start align-middle">Koordinat Presensi</th>
                        <th width="8%" class="text-center align-middle">Kecocokan</th>
                        <th width="11%" class="text-start align-middle">Status / Keterangan</th>
                        <th width="7%" class="text-center align-middle">Bukti</th>
                        <th width="6%" class="text-center align-middle">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensiReport as $index => $presensi)
                        @php
                            $kecocokanData = $presensi->calculateKecocokan();
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="text-center align-middle small">
                                {{ $presensi->tanggal->translatedFormat('d/m/Y') }}
                            </td>
                            <td class="align-middle">
                                <strong>{{ $presensi->user->nama_lengkap }}</strong>
                                <div class="small text-muted">{{ $presensi->user->kelas?->tingkat }}-{{ $presensi->user->kelas?->nama_kelas }} ({{ $presensi->user->jurusan?->nama_jurusan }})</div>
                            </td>
                            <td class="align-middle">
                                <span class="fw-semibold text-secondary small">
                                    <i class="fas fa-building me-1 text-muted"></i>{{ $presensi->user->mitra?->nama_mitra ?? $presensi->user->mitra_magang ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="fw-bold text-dark"><i class="far fa-clock me-1 text-success"></i>{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-' }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="fw-bold text-dark"><i class="far fa-clock me-1 text-danger"></i>{{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '-' }}</span>
                            </td>
                            <td class="align-middle">
                                @if($presensi->status === 'hadir' && $presensi->latitude_masuk && $presensi->longitude_masuk)
                                    <a href="https://www.google.com/maps?q={{ $presensi->latitude_masuk }},{{ $presensi->longitude_masuk }}" target="_blank" class="btn btn-sm btn-outline-danger w-100 text-start">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ round($presensi->latitude_masuk, 5) }}, {{ round($presensi->longitude_masuk, 5) }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if($presensi->status === 'hadir')
                                    @if($kecocokanData)
                                        @php
                                            $pct = $kecocokanData['percentage'];
                                            $badgeColor = $pct < 70 ? 'danger' : ($pct < 90 ? 'warning' : 'success');
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} fs-6 py-2 px-3">{{ $pct }}%</span>
                                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">Jarak: {{ \App\Helpers\LocationHelper::formatDistance($kecocokanData['distance']) }}</div>
                                    @else
                                        @if(!$presensi->user->latitude || !$presensi->user->longitude)
                                            <span class="badge bg-light text-warning border border-warning" style="font-size: 0.75rem;">Koordinat Belum Diset</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-{{ $presensi->status == 'hadir' ? 'success' : ($presensi->status == 'izin' ? 'warning' : ($presensi->status == 'sakit' ? 'info' : 'danger')) }} mb-1">
                                    {{ ucfirst($presensi->status) }}
                                </span>
                                @if($presensi->keterangan)
                                    <div class="small text-secondary fw-semibold">{{ $presensi->keterangan }}</div>
                                @endif
                            </td>
                        <td class="text-center align-middle">
                                @if($presensi->hasBuktiFoto())
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#buktiModal"
                                            data-nama="{{ $presensi->user->nama_lengkap }}"
                                            data-tanggal="{{ $presensi->tanggal->translatedFormat('d F Y') }}"
                                            data-status="{{ ucfirst($presensi->status) }}"
                                            data-keterangan="{{ $presensi->keterangan ?? '-' }}"
                                            data-foto="{{ $presensi->buktiFotoUrl() }}"
                                            data-download="{{ route('admin.presensi.bukti.download', $presensi) }}"
                                            data-detail="{{ route('admin.presensi.bukti', $presensi) }}">
                                        <i class="fas fa-image me-1"></i>Lihat Detail
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            {{-- Kolom Aksi Edit Status --}}
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm btn-outline-warning"
                                        title="Edit Status Presensi"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editStatusModal"
                                        data-presensi-id="{{ $presensi->id }}"
                                        data-nama="{{ $presensi->user->nama_lengkap }}"
                                        data-tanggal="{{ $presensi->tanggal->translatedFormat('d F Y') }}"
                                        data-status="{{ $presensi->status }}"
                                        data-keterangan="{{ $presensi->keterangan ?? '' }}"
                                        data-jam-masuk="{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '' }}"
                                        data-jam-keluar="{{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '' }}"
                                        data-update-url="{{ route('admin.presensi.update-status', $presensi) }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">Belum ada data presensi dalam 7 hari terakhir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Edit Status Presensi --}}
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
document.getElementById('buktiModal')?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('buktiNama').textContent = button.getAttribute('data-nama') || '-';
    document.getElementById('buktiTanggal').textContent = button.getAttribute('data-tanggal') || '-';
    document.getElementById('buktiStatus').textContent = button.getAttribute('data-status') || '-';
    document.getElementById('buktiKeterangan').textContent = button.getAttribute('data-keterangan') || '-';
    document.getElementById('buktiFotoPreview').src = button.getAttribute('data-foto') || '';
    document.getElementById('buktiDownloadBtn').href = button.getAttribute('data-download') || '#';
    document.getElementById('buktiFullPageBtn').href = button.getAttribute('data-detail') || '#';
});

// Modal Edit Status Presensi
document.getElementById('editStatusModal')?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const form = document.getElementById('editStatusForm');

    // Set nama & tanggal info
    document.getElementById('editNamaSiswa').textContent = button.getAttribute('data-nama') || '-';
    document.getElementById('editTanggal').textContent = 'Tanggal: ' + (button.getAttribute('data-tanggal') || '-');

    // Set nilai form
    const statusSelect = document.getElementById('editStatus');
    const currentStatus = button.getAttribute('data-status') || 'alfa';
    statusSelect.value = currentStatus;

    document.getElementById('editKeterangan').value = button.getAttribute('data-keterangan') || '';
    document.getElementById('editJamMasuk').value  = button.getAttribute('data-jam-masuk') || '';
    document.getElementById('editJamKeluar').value = button.getAttribute('data-jam-keluar') || '';

    // Set action URL form
    form.action = button.getAttribute('data-update-url');

    // Toggle jam group visibility
    toggleJamGroup(currentStatus);
});

function toggleJamGroup(status) {
    const jamGroup = document.getElementById('jamGroup');
    if (status === 'hadir') {
        jamGroup.style.display = '';
    } else {
        jamGroup.style.display = 'none';
    }
}

document.getElementById('editStatus')?.addEventListener('change', function () {
    toggleJamGroup(this.value);
});
</script>
@endpush
@endsection
