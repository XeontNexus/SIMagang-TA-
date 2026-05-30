@extends('layouts.app')

@section('title', 'Daftar List Siswa - SIMagang')
@section('page-title', 'Daftar List Siswa')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar List Informasi Siswa Magang</h6>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.students.list') }}" class="row g-3 mb-4" id="filterForm">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Cari Siswa</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama/Username/Sekolah/Mitra..."
                       value="{{ request('search') }}" id="searchInput">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Filter Status</label>
                <select name="status" class="form-select form-select-sm" id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Filter Kelas</label>
                <select name="kelas_id" class="form-select form-select-sm" id="kelasFilter">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->tingkat }}-{{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <div class="w-100">
                    <label class="form-label small fw-bold">Filter Jurusan</label>
                    <select name="jurusan_id" class="form-select form-select-sm" id="jurusanFilter">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $jur)
                            <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <a href="{{ route('admin.students.list') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter" style="height: 31px;">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>

        <!-- Information Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th width="4%" class="py-2 text-center">No</th>
                        <th width="14%">Nama Lengkap</th>
                        <th width="11%">Kelas & Jurusan</th>
                        <th width="14%">Periode Magang</th>
                        <th width="14%">Guru Pembimbing</th>
                        <th width="13%">Mitra Magang</th>
                        <th width="14%">Alamat Magang</th>
                        <th width="10%" class="text-center">Total Presensi</th>
                        <th width="6%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr>
                            <td class="py-2 align-middle text-center"><strong>{{ $students->firstItem() + $index }}</strong></td>
                            <td class="align-middle">
                                <strong>{{ $student->nama_lengkap }}</strong>
                                <div class="small text-muted">{{ $student->username }}</div>
                            </td>
                            <td class="align-middle">
                                @if($student->kelas)
                                    <span class="badge bg-light text-dark border">{{ $student->kelas->tingkat }}-{{ $student->kelas->nama_kelas }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                                <div class="small text-secondary mt-1">{{ $student->jurusan?->nama_jurusan ?? '-' }}</div>
                            </td>
                            <td class="align-middle">
                                @if($student->tanggal_mulai && $student->tanggal_selesai)
                                    <div class="small fw-semibold">{{ $student->tanggal_mulai->format('d/m/Y') }} - {{ $student->tanggal_selesai->format('d/m/Y') }}</div>
                                    <small class="text-muted">({{ $student->tanggal_mulai->diffInDays($student->tanggal_selesai) + 1 }} hari)</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($student->guruPembimbing)
                                    <div class="fw-semibold text-secondary small">{{ $student->guruPembimbing->nama_guru }}</div>
                                    @if($student->guruPembimbing->no_hp)
                                        <small class="text-muted"><i class="fab fa-whatsapp text-success me-1"></i>{{ $student->guruPembimbing->no_hp }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($student->mitra_magang)
                                    <span class="fw-semibold text-primary"><i class="fas fa-building me-1"></i>{{ $student->mitra_magang }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle small">
                                {{ $student->alamat_magang ?? '-' }}
                            </td>
                            <td class="align-middle text-center">
                                @php
                                    $totalPresensi = $student->presensis->count();
                                    $hadirCount = $student->presensis->where('status','hadir')->count();
                                @endphp
                                @if($totalPresensi > 0)
                                    <span class="badge bg-primary">{{ $hadirCount }}<small class="fw-normal"> Hadir</small></span>
                                    <div class="small text-muted mt-1">{{ $totalPresensi }} total</div>
                                @else
                                    <span class="text-muted small">Belum ada</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-info" title="Detail Informasi Siswa">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Tidak ada data siswa magang</td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $students->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const kelasFilter = document.getElementById('kelasFilter');
    const jurorFilter = document.getElementById('jurusanFilter');
    let searchTimeout;

    function submitForm() {
        filterForm.submit();
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(submitForm, 500);
    });

    statusFilter.addEventListener('change', submitForm);
    kelasFilter.addEventListener('change', submitForm);
    jurorFilter.addEventListener('change', submitForm);
</script>
@endpush
@endsection
