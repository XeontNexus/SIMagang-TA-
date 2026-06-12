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
                <input type="text" name="search" class="form-control" placeholder="Cari nama/username/email..."
                       value="{{ request('search') }}" id="searchInput">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select" id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-1"></i>Reset
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
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
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-warning" title="Edit Akun">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($student->no_hp)
                                        @php
                                            $waMessage = "Halo *" . $student->nama_lengkap . "*,\n\nAdmin SIMagang menginformasikan bahwa *akun PKL Anda sudah siap digunakan*.\n\nDetail login:\nUsername: *" . $student->username . "*\nPassword: *" . ($student->password_plain ?? $student->nisn ?? '[Password Anda]') . "*\n\nLink login: " . url('/login') . "\n\n_Segera ganti password setelah login pertama demi keamanan._";
                                            $waPhone = preg_replace('/[^0-9]/', '', $student->no_hp);
                                            if (str_starts_with($waPhone, '0')) {
                                                $waPhone = '62' . substr($waPhone, 1);
                                            }
                                            $waUrl = "https://wa.me/" . $waPhone . "?text=" . rawurlencode($waMessage);
                                        @endphp
                                        <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-success" title="Kirim Info Akun via WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
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
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada data siswa</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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
    
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500); // Delay 500ms sebelum submit
    });
    
    statusFilter.addEventListener('change', () => {
        searchForm.submit();
    });
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
</script>
@endpush
@endsection
