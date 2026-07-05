@extends('layouts.app')

@section('title', 'Kelola Akun Siswa - SIMagang')
@section('page-title', 'Kelola Akun Siswa')

@section('content')
{{-- Alert untuk warning message --}}
@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Peringatan:</strong> {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow">
    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa Magang</h6>
        <div class="d-flex gap-1">
            <a href="{{ route('admin.import.index') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-import me-1"></i>Import Data
            </a>
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Tambah Siswa
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3 mb-4" id="searchForm">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari nama/username/nomor telpon..."
                       value="{{ request('search') }}" id="searchInput">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select" id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="belum_dinotifikasi" {{ request('status') == 'belum_dinotifikasi' ? 'selected' : '' }}>Belum Dinotifikasi</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-1"></i>Reset
                </a>
            </div>
        </form>

        {{-- Bulk Action Toolbar --}}
        <div id="bulkActionToolbar" class="d-none mb-3 p-3 bg-light rounded border d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white" id="selectedCount">0</span>
                <span class="fw-semibold text-dark">akun siswa terpilih</span>
            </div>
            <button type="button" class="btn btn-danger btn-sm" id="btnBulkDelete">
                <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                        </th>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>No. WhatsApp</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" value="{{ $student->id }}" class="form-check-input student-checkbox">
                            </td>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>
                                {{ $student->nama_lengkap }}
                                <div class="small text-muted">{{ $student->username }}</div>
                            </td>
                            <td>
                                @if($student->no_hp)
                                    <span>{{ $student->no_hp }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>@include('partials.student-status-badge', ['status' => $student->status])</td>
                            <td>
                                <div class="d-flex gap-1 align-items-center">
                                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-warning" title="Edit Akun">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($student->no_hp)
                                        @php
                                            // Selalu buat pesan terbaru saja — tidak menyimpan riwayat, link WA selalu fresh
                                            $waPassword = $student->password_plain ?? '[Password Anda]';
                                            $waMessage = implode("\n", [
                                                "Halo *" . $student->nama_lengkap . "*,",
                                                "",
                                                "Berikut informasi akun SIMagang Anda (terbaru):",
                                                "Username: *" . $student->username . "*",
                                                "Password: *" . $waPassword . "*",
                                                "Link Login: " . url('/login'),
                                                "",
                                                "_Segera ganti password setelah login pertama demi keamanan._",
                                            ]);
                                            $waPhone = preg_replace('/[^0-9]/', '', $student->no_hp);
                                            if (str_starts_with($waPhone, '0')) {
                                                $waPhone = '62' . substr($waPhone, 1);
                                            }
                                            $waUrl = "https://wa.me/" . $waPhone . "?text=" . rawurlencode($waMessage);
                                        @endphp
                                        <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-success" title="Kirim Info Akun via WhatsApp" onclick="triggerWaAndMarkNotified(event, '{{ $student->id }}', '{{ $waUrl }}')">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif
                                    @if($student->status === 'belum_dinotifikasi')
                                        <form action="{{ route('admin.students.mark-as-notified', $student) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info" title="Tandai Sudah Dinotifikasi">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form id="delete-form-{{ $student->id }}" action="{{ route('admin.students.destroy', $student) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                                onclick="confirmDelete('{{ $student->id }}', '{{ $student->nama_lengkap }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada data siswa</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Hidden form for bulk delete --}}
        <form id="bulkDeleteForm" action="{{ route('admin.students.bulk-delete') }}" method="POST" class="d-none">
            @csrf
            <div id="bulkDeleteIdsContainer"></div>
        </form>

        {{ $students->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Real-time search
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;
    
    function performSearch() {
        // Auto-submit form setelah user berhenti mengetik
        searchForm.submit();
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500); // Delay 500ms sebelum submit
        });
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            searchForm.submit();
        });
    }

    // Bulk Delete Selection & Actions
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const bulkActionToolbar = document.getElementById('bulkActionToolbar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    function toggleBulkToolbar() {
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
        if (selectedCountSpan) selectedCountSpan.textContent = checkedCount;
        if (checkedCount > 0) {
            bulkActionToolbar.classList.remove('d-none');
            bulkActionToolbar.classList.add('d-flex');
        } else {
            bulkActionToolbar.classList.add('d-none');
            bulkActionToolbar.classList.remove('d-flex');
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            studentCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            toggleBulkToolbar();
        });
    }

    studentCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const allChecked = Array.from(studentCheckboxes).every(c => c.checked);
            if (selectAllCheckbox) selectAllCheckbox.checked = allChecked;
            toggleBulkToolbar();
        });
    });

    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', () => {
            const checkedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
            const checkedCount = checkedCheckboxes.length;
            
            Swal.fire({
                title: 'Hapus Beberapa Akun Siswa?',
                html: `Apakah Anda yakin ingin menghapus <strong>${checkedCount}</strong> akun siswa yang terpilih?<br><small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Tindakan ini tidak dapat dibatalkan!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus Semua!',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const container = document.getElementById('bulkDeleteIdsContainer');
                    if (container) {
                        container.innerHTML = '';
                        checkedCheckboxes.forEach(cb => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = cb.value;
                            container.appendChild(input);
                        });
                    }
                    bulkDeleteForm.submit();
                }
            });
        });
    }

    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Akun Siswa?',
            html: `Apakah Anda yakin ingin menghapus akun <strong>${nama}</strong>?<br><small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Tindakan ini tidak dapat dibatalkan!</small>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: '<i class="fas fa-times me-1"></i> Tidak',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Trigger WhatsApp & Mark as Notified via AJAX
    function triggerWaAndMarkNotified(event, studentId, waUrl) {
        event.preventDefault();
        
        // Open WhatsApp in a new tab immediately
        window.open(waUrl, '_blank');
        
        // Send AJAX request to mark as notified
        fetch(`/admin/students/${studentId}/mark-as-notified`, {
            method: 'POST',
            headers: {
                'X-CSR-Token': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                // Refresh to show status changed from 'Belum Dinotifikasi' to 'Menunggu'
                window.location.reload();
            }
        }).catch(error => {
            console.error('Error updating status:', error);
        });
    }
</script>
@endpush
@endsection
