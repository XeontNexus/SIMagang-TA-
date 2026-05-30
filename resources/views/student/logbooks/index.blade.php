@extends('layouts.app')

@section('title', 'Logbook - SIMagang')
@section('page-title', 'Logbook Magang')

@section('content')
<!-- Form Input Logbook -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-plus-circle me-2"></i>Buat Logbook Baru
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('student.logbooks.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Minggu Ke *</label>
                            <select name="minggu_ke" class="form-select @error('minggu_ke') is-invalid @enderror" required>
                                <option value="">-- Pilih Minggu --</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('minggu_ke') == $i ? 'selected' : '' }}>Minggu Ke-{{ $i }}</option>
                                @endfor
                            </select>
                            @error('minggu_ke')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Bulan *</label>
                            <select name="bulan" class="form-select @error('bulan') is-invalid @enderror" required>
                                <option value="">-- Pilih Bulan --</option>
                                @php
                                    $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                @endphp
                                @foreach($bulanList as $index => $namaBulan)
                                    <option value="{{ $index + 1 }}" {{ old('bulan', now()->month) == ($index + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                            @error('bulan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kegiatan *</label>
                        <textarea name="kegiatan" class="form-control @error('kegiatan') is-invalid @enderror" 
                                  rows="3" placeholder="Jelaskan kegiatan yang Anda lakukan...">{{ old('kegiatan') }}</textarea>
                        @error('kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Hasil</label>
                        <textarea name="hasil" class="form-control @error('hasil') is-invalid @enderror" 
                                  rows="2" placeholder="Hasil atau output dari kegiatan...">{{ old('hasil') }}</textarea>
                        @error('hasil')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kendala</label>
                        <textarea name="kendala" class="form-control @error('kendala') is-invalid @enderror" 
                                  rows="2" placeholder="Masalah atau kendala yang dihadapi...">{{ old('kendala') }}</textarea>
                        @error('kendala')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Solusi</label>
                        <textarea name="solusi" class="form-control @error('solusi') is-invalid @enderror" 
                                  rows="2" placeholder="Cara mengatasi kendala...">{{ old('solusi') }}</textarea>
                        @error('solusi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Simpan Logbook
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Logbook dengan Filter -->
<div class="row">
    <div class="col-12">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-2"></i>Data Logbook Saya
                </h6>
            </div>
            <div class="card-body">

                <!-- Data Logbook Tabel -->
                @if($logbooks && $logbooks->count() > 0)
                    @php
                        // Deteksi duplikat bulan-minggu
                        $bulanMingguCount = [];
                        $duplicateWarnings = [];
                        foreach($logbooks as $logbook) {
                            $key = $logbook->tanggal_mulai->month . '-' . $logbook->minggu_ke;
                            if (!isset($bulanMingguCount[$key])) {
                                $bulanMingguCount[$key] = 0;
                            }
                            $bulanMingguCount[$key]++;
                            
                            if ($bulanMingguCount[$key] > 1) {
                                $duplicateWarnings[$key] = true;
                            }
                        }
                        
                        // Group logbooks by month
                        $logbooksByMonth = [];
                        foreach($logbooks as $logbook) {
                            $month = $logbook->tanggal_mulai->month;
                            if (!isset($logbooksByMonth[$month])) {
                                $logbooksByMonth[$month] = [];
                            }
                            $logbooksByMonth[$month][] = $logbook;
                        }
                    @endphp
                    
                    @if(count($duplicateWarnings) > 0)
                        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Peringatan!</strong> Ditemukan logbook dengan bulan dan minggu yang sama. Setiap bulan hanya dapat memiliki maksimal 5 logbook (1 per minggu).
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Loop by Month -->
                    @foreach($logbooksByMonth as $month => $monthLogbooks)
                        <div class="mb-4">
                            <!-- Month Header -->
                            <div class="alert mb-3" style="background-color: #0d6efd; color: white; border: none;">
                                <strong>BULAN: {{ strtoupper($bulanLabels[$month]) }} {{ now()->year }}</strong>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-sm table-bordered">
                                    <thead style="background-color: #d3d3d3;">
                                        <tr>
                                            <th width="12%" class="align-middle">Minggu</th>
                                            <th width="20%" class="align-middle">Kegiatan</th>
                                            <th width="18%" class="align-middle">Hasil</th>
                                            <th width="18%" class="align-middle">Kendala</th>
                                            <th width="15%" class="align-middle">Solusi</th>
                                            <th width="10%" class="align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthLogbooks as $logbook)
                                            <tr style="vertical-align: middle;">
                                                <td class="small align-middle text-center fw-bold">
                                                    <i class="fas fa-calendar me-1"></i>{{ sprintf('%02d', $logbook->minggu_ke) }}
                                                </td>
                                                <!-- Kegiatan -->
                                                <td class="small align-middle">
                                                    @php
                                                        $kegiatan = $logbook->kegiatan ?? '-';
                                                        $kegiatanShort = strlen($kegiatan) > 50 ? substr($kegiatan, 0, 50) : $kegiatan;
                                                        $needsExpand = strlen($kegiatan) > 50;
                                                    @endphp
                                                    <span class="text-content-short-{{ $logbook->id }}-kegiatan">
                                                        {{ $kegiatanShort }}@if($needsExpand)<span class="ms-1" style="cursor: pointer; color: #999; font-style: italic; font-weight: 400; font-size: 0.85em;" onclick="toggleContent('{{ $logbook->id }}')">...selengkapnya</span>@endif
                                                    </span>
                                                    <span class="text-content-full-{{ $logbook->id }}-kegiatan d-none">
                                                        {{ $kegiatan }}@if($needsExpand)<span class="ms-1" style="cursor: pointer; color: #999; font-style: italic; font-weight: 400; font-size: 0.85em;" onclick="toggleContent('{{ $logbook->id }}')">...lihat sedikit</span>@endif
                                                    </span>
                                                </td>
                                                <!-- Hasil -->
                                                <td class="small align-middle">
                                                    @php
                                                        $hasil = $logbook->hasil ?? '-';
                                                        $hasilShort = strlen($hasil) > 40 ? substr($hasil, 0, 40) : $hasil;
                                                        $hasilNeedsExpand = strlen($hasil) > 40;
                                                    @endphp
                                                    <span class="text-content-short-{{ $logbook->id }}-hasil">
                                                        {{ $hasilShort }}@if($hasilNeedsExpand)<span class="ms-1" style="cursor: pointer; color: #999; font-style: italic; font-weight: 400; font-size: 0.85em;" onclick="toggleContent('{{ $logbook->id }}')">...selengkapnya</span>@endif
                                                    </span>
                                                    <span class="text-content-full-{{ $logbook->id }}-hasil d-none">
                                                        {{ $hasil }}@if($hasilNeedsExpand)<span class="ms-1" style="cursor: pointer; color: #999; font-style: italic; font-weight: 400; font-size: 0.85em;" onclick="toggleContent('{{ $logbook->id }}')">...lihat sedikit</span>@endif
                                                    </span>
                                                </td>
                                                <!-- Kendala -->
                                                <td class="small align-middle">
                                                    @php
                                                        $kendala = $logbook->kendala ?? '-';
                                                        $kendalaShort = strlen($kendala) > 40 ? substr($kendala, 0, 40) : $kendala;
                                                        $kendalaNeedsExpand = strlen($kendala) > 40;
                                                    @endphp
                                                    <span class="text-content-short-{{ $logbook->id }}-kendala">
                                                        {{ $kendalaShort }}@if($kendalaNeedsExpand)<span class="ms-1" style="cursor: pointer; color: #999; font-style: italic; font-weight: 400; font-size: 0.85em;" onclick="toggleContent('{{ $logbook->id }}')">...selengkapnya</span>@endif
                                                    </span>
                                                    <span class="text-content-full-{{ $logbook->id }}-kendala d-none">
                                                        {{ $kendala }}@if($kendalaNeedsExpand)<span class="ms-1" style="cursor: pointer; color: #999; font-style: italic; font-weight: 400; font-size: 0.85em;" onclick="toggleContent('{{ $logbook->id }}')">...lihat sedikit</span>@endif
                                                    </span>
                                                </td>
                                                <!-- Solusi -->
                                                <td class="small align-middle">
                                                    @php
                                                        $solusi = $logbook->solusi ?? '-';
                                                        $solusiBefore = strlen($solusi) > 40 ? substr($solusi, 0, 40) : $solusi;
                                                        $solusiBelumExpand = strlen($solusi) > 40;
                                                    @endphp
                                                    <span class="text-content-short-{{ $logbook->id }}-solusi">
                                                        {{ $solusiBefore }}@if($solusiBelumExpand)<span class="ms-1" style="cursor: pointer; color: #999; font-style: italic; font-weight: 400; font-size: 0.85em;" onclick="toggleContent('{{ $logbook->id }}')">...selengkapnya</span>@endif
                                                    </span>
                                                    <span class="text-content-full-{{ $logbook->id }}-solusi d-none">
                                                        {{ $solusi }}@if($solusiBelumExpand)<span class="ms-1" style="cursor: pointer; color: #999; font-style: italic; font-weight: 400; font-size: 0.85em;" onclick="toggleContent('{{ $logbook->id }}')">...lihat sedikit</span>@endif
                                                    </span>
                                                </td>
                                                <td class="small align-middle">
                                                    <div class="d-flex gap-2 justify-content-center flex-wrap align-items-center">
                                                        @if($logbook->status != 'approved')
                                                            <a href="{{ route('student.logbooks.edit', $logbook) }}" class="btn btn-sm btn-warning px-3" title="Edit">
                                                                <i class="fas fa-edit me-1"></i>Edit
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-danger px-3" onclick="confirmDelete({{ $logbook->id }})" title="Hapus">
                                                                <i class="fas fa-trash me-1"></i>Hapus
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0">Belum ada logbook untuk periode ini</p>
                        <small>Gunakan navigasi di atas untuk memilih periode yang berbeda.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus logbook ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Track expanded state per logbook (each week independent)
let expandedLogbooks = {};

function toggleContent(logbookId, contentType) {
    // Check current state of this logbook
    const isCurrentlyExpanded = expandedLogbooks[logbookId];
    
    // Toggle this logbook - each week can expand/collapse independently
    const newState = !isCurrentlyExpanded;
    expandedLogbooks[logbookId] = newState;
    
    // Apply new state to ALL content fields in this logbook
    const contentTypes = ['kegiatan', 'hasil', 'kendala', 'solusi'];
    
    contentTypes.forEach(type => {
        const shortEl = document.querySelector(`.text-content-short-${logbookId}-${type}`);
        const fullEl = document.querySelector(`.text-content-full-${logbookId}-${type}`);
        
        if (shortEl && fullEl) {
            if (newState) {
                // Expand
                shortEl.classList.add('d-none');
                fullEl.classList.remove('d-none');
            } else {
                // Collapse
                shortEl.classList.remove('d-none');
                fullEl.classList.add('d-none');
            }
        }
    });
}

function confirmDelete(logbookId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '/student/logbooks/' + logbookId;
    modal.show();
}
</script>

<!-- Modal Detail Logbook (tersimpan untuk viewing) -->
@if($logbooks && $logbooks->count() > 0)
    @foreach($logbooks as $logbook)
        <!-- Modal Lihat Detail -->
        <div class="modal fade" id="logbookViewModal{{ $logbook->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Logbook Minggu {{ $logbook->minggu_ke }} - {{ $bulanLabels[$logbook->tanggal_mulai->month] }} {{ $logbook->tanggal_mulai->year }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Bulan</label>
                                <p class="fw-bold">{{ $bulanLabels[$logbook->tanggal_mulai->month] }} {{ $logbook->tanggal_mulai->year }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Minggu Ke</label>
                                <p class="fw-bold">{{ $logbook->minggu_ke }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Periode</label>
                                <p class="fw-bold">{{ $logbook->tanggal_mulai->format('d/m/Y') }} - {{ $logbook->tanggal_selesai->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Waktu Upload</label>
                                <p class="fw-bold">{{ $logbook->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Kegiatan</label>
                            <p>{{ $logbook->kegiatan }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Hasil</label>
                            <p>{{ $logbook->hasil ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Kendala</label>
                            <p>{{ $logbook->kendala ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Solusi</label>
                            <p>{{ $logbook->solusi ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Status</label>
                            <p>
                                <span class="badge bg-{{ 
                                    $logbook->status == 'approved' ? 'success' : 
                                    ($logbook->status == 'rejected' ? 'danger' : 
                                    ($logbook->status == 'submitted' ? 'warning' : 'secondary')) 
                                }}">
                                    {{ 
                                        $logbook->status == 'approved' ? '✓ Disetujui' : 
                                        ($logbook->status == 'rejected' ? '✗ Ditolak' : 
                                        ($logbook->status == 'submitted' ? '⏳ Menunggu' : 'Draft'))
                                    }}
                                </span>
                            </p>
                        </div>
                        @if($logbook->status == 'rejected' && $logbook->catatan_admin)
                            <div class="alert alert-warning">
                                <label class="text-muted small fw-bold">Catatan dari Admin</label>
                                <p class="mb-0">{{ $logbook->catatan_admin }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        @if($logbook->status != 'approved')
                            <a href="{{ route('student.logbooks.edit', $logbook) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
</div>

@endsection
