@extends('layouts.app')

@section('title', 'Logbook - SIMagang')
@section('page-title', 'Kelola Logbook')

@section('content')
<!-- Nav Tabs -->
<ul class="nav nav-tabs logbook-admin-tabs mb-4 flex-nowrap" role="tablist">
    <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link active w-100" id="approval-tab" data-bs-toggle="tab" data-bs-target="#approvalTab" type="button" role="tab">
            <i class="fas fa-check-circle d-none d-sm-inline me-sm-2"></i>
            <span class="logbook-tab-label">Approval Logbook</span>
        </button>
    </li>
    <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link w-100" id="data-tab" data-bs-toggle="tab" data-bs-target="#dataTab" type="button" role="tab">
            <i class="fas fa-graduation-cap d-none d-sm-inline me-sm-2"></i>
            <span class="logbook-tab-label">Data Logbook Siswa</span>
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <!-- Tab 1: Approval Logbook -->
    <div class="tab-pane fade show active" id="approvalTab" role="tabpanel">
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Logbook Siswa untuk Approval</h6>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.logbooks.index') }}" class="row g-3 mb-4" id="approvalFilterForm">
            <div class="col-md-4">
                <select name="status" class="form-select" id="statusApprovalFilter">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted (Menunggu ACC)</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama siswa..." 
                       value="{{ request('search') }}" id="searchLogbook">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('admin.logbooks.index') }}" class="btn btn-secondary">
                    <i class="fas fa-sync-alt"></i>Reset
                </a>
            </div>
        </form>

        <!-- Action Buttons -->
        <form id="bulkActionForm" method="POST" class="mb-3" style="display: none;">
            @csrf
            <div class="row align-items-center g-2">
                <div class="col-md-6">
                    <div class="alert alert-info mb-0">
                        <span id="selectedCount">0</span> logbook dipilih
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-success btn-sm" id="bulkApproveBtn" onclick="bulkApprove()">
                        <i class="fas fa-check-circle me-1"></i>Approve Dipilih
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="bulkRejectBtn" onclick="bulkReject()">
                        <i class="fas fa-times-circle me-1"></i>Reject Dipilih
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Minggu Ke</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logbooks as $index => $logbook)
                        <tr>
                            <td>
                                <input type="checkbox" class="logbook-checkbox" value="{{ $logbook->id }}" onchange="updateSelectedCount()">
                            </td>
                            <td>{{ $logbooks->firstItem() + $index }}</td>
                            <td>{{ $logbook->user->nama_lengkap }}</td>
                            <td>{{ $logbook->minggu_ke }}</td>
                            <td>{{ $logbook->tanggal_mulai->format('d/m/Y') }} - {{ $logbook->tanggal_selesai->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $logbook->status == 'approved' ? 'success' : ($logbook->status == 'rejected' ? 'danger' : ($logbook->status == 'submitted' ? 'warning' : 'secondary')) }}">
                                    {{ ucfirst($logbook->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#logbookModal{{ $logbook->id }}" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($logbook->status == 'submitted' || $logbook->status == 'rejected')
                                        <form action="{{ route('admin.logbooks.approve', $logbook) }}" method="POST" class="m-0" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve Langsung">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="openRejectModal({{ $logbook->id }}, '{{ $logbook->user->nama_lengkap }}')" title="Reject dengan Catatan">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Lihat Detail Logbook -->
                        <div class="modal fade" id="logbookModal{{ $logbook->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">Detail Logbook - {{ $logbook->user->nama_lengkap }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="text-muted">Minggu Ke</label>
                                                <p class="fw-bold">{{ $logbook->minggu_ke }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted">Periode</label>
                                                <p class="fw-bold">{{ $logbook->tanggal_mulai->format('d/m/Y') }} - {{ $logbook->tanggal_selesai->format('d/m/Y') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted">Bulan</label>
                                                <p class="fw-bold">
                                                    @php
                                                        $bulanLabels = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    @endphp
                                                    {{ $bulanLabels[$logbook->tanggal_mulai->month] }} {{ $logbook->tanggal_mulai->year }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted">Guru Pembimbing</label>
                                                <p class="fw-bold">{{ $logbook->user->mitra->guru_pembimbing->nama_lengkap ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="mb-3">
                                            <label class="text-muted">Kegiatan</label>
                                            <p>{{ $logbook->kegiatan }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted">Hasil</label>
                                            <p>{{ $logbook->hasil ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted">Kendala</label>
                                            <p>{{ $logbook->kendala ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted">Solusi</label>
                                            <p>{{ $logbook->solusi ?? '-' }}</p>
                                        </div>
                                        @if($logbook->catatan_admin)
                                            <div class="alert alert-warning">
                                                <label class="text-muted">Catatan Admin</label>
                                                <p class="mb-0">{{ $logbook->catatan_admin }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        @if($logbook->status == 'submitted' || $logbook->status == 'rejected')
                                            <form action="{{ route('admin.logbooks.approve', $logbook) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger" onclick="openRejectModal({{ $logbook->id }}, '{{ $logbook->user->nama_lengkap }}'); $('.modal').modal('hide');">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data logbook</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $logbooks->links() }}
    </div>
</div>

<!-- Modal Reject dengan Catatan -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Logbook</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Reject logbook untuk <strong id="rejectStudentName"></strong></p>
                    <div class="mb-3">
                        <label for="rejectNotes" class="form-label">Catatan untuk Revisi *</label>
                        <textarea class="form-control" id="rejectNotes" name="catatan_admin" rows="4" placeholder="Masukkan catatan untuk revisi..." required></textarea>
                        <small class="text-muted">Catatan akan dikirimkan ke siswa untuk melakukan revisi.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i>Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.logbook-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selected;
        document.getElementById('bulkActionForm').style.display = selected > 0 ? 'block' : 'none';
    }

    function toggleSelectAll() {
        const isChecked = document.getElementById('selectAll').checked;
        document.querySelectorAll('.logbook-checkbox').forEach(cb => cb.checked = isChecked);
        updateSelectedCount();
    }

    function openRejectModal(logbookId, studentName) {
        document.getElementById('rejectStudentName').textContent = studentName;
        document.getElementById('rejectForm').action = '/admin/logbooks/' + logbookId + '/reject';
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }

    function bulkApprove() {
        const selected = document.querySelectorAll('.logbook-checkbox:checked');
        if (selected.length === 0) {
            alert('Pilih minimal satu logbook');
            return;
        }
        
        if (confirm('Approve ' + selected.length + ' logbook terpilih?')) {
            selected.forEach(cb => {
                const logbookId = cb.value;
                fetch('/admin/logbooks/' + logbookId + '/approve', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
            });
            setTimeout(() => location.reload(), 500);
        }
    }

    function bulkReject() {
        alert('Untuk reject multiple, gunakan form reject individual untuk memberikan catatan spesifik ke setiap siswa.');
    }

    // Auto-submit filter saat status berubah
    const statusApprovalFilter = document.getElementById('statusApprovalFilter');
    if (statusApprovalFilter) {
        statusApprovalFilter.addEventListener('change', function() {
            document.getElementById('approvalFilterForm').submit();
        });
    }

    // Real-time search dengan delay
    const searchLogbookInput = document.getElementById('searchLogbook');
    if (searchLogbookInput) {
        searchLogbookInput.addEventListener('keyup', function() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                document.getElementById('approvalFilterForm').submit();
            }, 500);
        });
    }
    // Auto-aktifkan tab berdasarkan query param ?tab=
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab === 'data') {
        const dataTabEl = document.getElementById('data-tab');
        if (dataTabEl) {
            const tabInstance = new bootstrap.Tab(dataTabEl);
            tabInstance.show();
        }
    }
</script>
@endpush
    </div><!-- End Tab Approval -->

    <!-- Tab 2: Data Logbook Siswa yang Disetujui -->
    <div class="tab-pane fade" id="dataTab" role="tabpanel">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-graduation-cap me-2"></i>Data Logbook Siswa yang Disetujui
                </h6>
            </div>
            <div class="card-body">
                <!-- Filter Search -->
                <form method="GET" action="{{ route('admin.logbooks.index') }}" class="row g-3 mb-4">
                    <input type="hidden" name="tab" value="data">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama siswa..." 
                               value="{{ request('search') }}" id="searchStudentData">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Cari
                        </button>
                    </div>
                </form>

                <!-- List Logbook Siswa -->
                <div class="accordion" id="studentLogbookAccordion">
                    @if(isset($approvedLogbooks) && $approvedLogbooks->count() > 0)
                        @php $prevStudent = null; @endphp
                        @foreach($approvedLogbooks->groupBy('user_id') as $studentId => $studentLogbooks)
                            @php 
                                $firstLogbook = $studentLogbooks->first();
                                $student = $firstLogbook->user;
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#student{{ $studentId }}">
                                        <div class="w-100">
                                            <div class="fw-bold">{{ $student->nama_lengkap }}</div>
                                            <small class="text-muted">
                                                @if($student->kelas)
                                                    <span class="me-2"><i class="fas fa-chalkboard me-1"></i>{{ $student->kelas->tingkat }}-{{ $student->kelas->nama_kelas }}</span>
                                                @endif
                                                @if($student->jurusan)
                                                    <span class="me-2"><i class="fas fa-graduation-cap me-1"></i>{{ $student->jurusan->nama_jurusan }}</span>
                                                @endif
                                                <span><i class="fas fa-book me-1"></i>{{ $studentLogbooks->count() }} Logbook</span>
                                            </small>
                                        </div>
                                    </button>
                                </h2>
                                <div id="student{{ $studentId }}" class="accordion-collapse collapse" data-bs-parent="#studentLogbookAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 80px;">Minggu</th>
                                                        <th>Periode</th>
                                                        <th style="width: 150px;">Guru Pembimbing</th>
                                                        <th style="width: 200px;">Kegiatan</th>
                                                        <th style="width: 100px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($studentLogbooks->sortBy('minggu_ke') as $logbook)
                                                        <tr>
                                                            <td class="fw-bold">Minggu {{ $logbook->minggu_ke }}</td>
                                                            <td class="small">{{ $logbook->tanggal_mulai->format('d/m/Y') }} - {{ $logbook->tanggal_selesai->format('d/m/Y') }}</td>
                                                            <td class="small">{{ $logbook->user->mitra->guru_pembimbing->nama_lengkap ?? '-' }}</td>
                                                            <td class="small">{{ Str::limit($logbook->kegiatan, 50) }}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                                                        data-bs-target="#dataLogbookModal{{ $logbook->id }}" title="Lihat Detail">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        <!-- Modal Detail Logbook Siswa -->
                                                        <div class="modal fade" id="dataLogbookModal{{ $logbook->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-success text-white">
                                                                        <h5 class="modal-title">Logbook {{ $student->nama_lengkap }} - Minggu {{ $logbook->minggu_ke }}</h5>
                                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row mb-3">
                                                                            <div class="col-md-6">
                                                                                <label class="text-muted small">Siswa</label>
                                                                                <p class="fw-bold">{{ $student->nama_lengkap }}</p>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="text-muted small">Kelas</label>
                                                                                <p class="fw-bold">{{ $student->kelas ? ($student->kelas->tingkat . '-' . $student->kelas->nama_kelas) : '-' }}</p>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="text-muted small">Jurusan</label>
                                                                                <p class="fw-bold">{{ $student->jurusan?->nama_jurusan ?? '-' }}</p>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="text-muted small">Guru Pembimbing</label>
                                                                                <p class="fw-bold">{{ $logbook->user->mitra->guru_pembimbing->nama_lengkap ?? '-' }}</p>
                                                                            </div>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="row mb-3">
                                                                            <div class="col-md-6">
                                                                                <label class="text-muted small">Bulan</label>
                                                                                <p class="fw-bold">
                                                                                    @php
                                                                                        $bulanLabels = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                                                    @endphp
                                                                                    {{ $bulanLabels[$logbook->tanggal_mulai->month] }} {{ $logbook->tanggal_mulai->year }}
                                                                                </p>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="text-muted small">Minggu Ke</label>
                                                                                <p class="fw-bold">{{ $logbook->minggu_ke }}</p>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label class="text-muted small">Periode</label>
                                                                                <p class="fw-bold">{{ $logbook->tanggal_mulai->format('d/m/Y') }} - {{ $logbook->tanggal_selesai->format('d/m/Y') }}</p>
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
                                                                        <div class="alert alert-success mb-0">
                                                                            <i class="fas fa-check-circle me-2"></i>
                                                                            <strong>Status: Disetujui</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data logbook siswa yang disetujui</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div><!-- End Tab Data Logbook Siswa -->
</div><!-- End Tab Content -->

@push('styles')
<style>
    .logbook-admin-tabs {
        display: flex;
        flex-wrap: nowrap;
        width: 100%;
    }

    .logbook-admin-tabs .nav-item {
        flex: 1 1 50%;
        min-width: 0;
        text-align: center;
    }

    .logbook-admin-tabs .nav-link {
        text-align: center;
        border-radius: 0;
        padding: 0.7rem 0.35rem;
        font-size: 0.78rem;
        line-height: 1.25;
        white-space: normal;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logbook-admin-tabs .nav-link.active {
        font-weight: 600;
    }

    @media (max-width: 575.98px) {
        .logbook-admin-tabs .logbook-tab-label {
            display: block;
            font-size: 0.72rem;
        }
    }

    @media (min-width: 576px) {
        .logbook-admin-tabs .nav-link {
            font-size: 0.95rem;
            padding: 0.65rem 1rem;
            white-space: nowrap;
        }
    }
</style>
@endpush
@endsection
