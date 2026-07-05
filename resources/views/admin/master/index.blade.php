@extends('layouts.app')

@section('title', 'Data Master - SIMagang')
@section('page-title', 'Data Master')

@push('styles')
<style>
    .table-scroll-6 {
        max-height: 280px;
        overflow-y: auto;
    }
    .table-scroll-10 {
        max-height: 450px;
        overflow-y: auto;
    }
    .table-scroll-6 thead th, .table-scroll-10 thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #f8f9fa;
        box-shadow: inset 0 -1px 0 #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Daftar Jurusan Table -->
    <div class="col-12 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Jurusan</h6>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createJurusanModal">
                    <i class="fas fa-plus me-1"></i>Tambah Jurusan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive table-scroll-10">
                    <table class="table table-bordered table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Kode Jurusan</th>
                                <th width="35%">Nama Jurusan</th>
                                <th width="25%">Deskripsi</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $counter = 1;
                            @endphp
                            
                            @forelse($jurusans as $jurusan)
                                <tr>
                                    <td><strong>{{ $counter++ }}</strong></td>
                                    <td>{{ $jurusan->kode_jurusan }}</td>
                                    <td>
                                        <strong>{{ $jurusan->nama_jurusan }}</strong>
                                    </td>
                                    <td>{{ Str::limit($jurusan->deskripsi, 30) ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editJurusanModal{{ $jurusan->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form id="delete-jurusan-{{ $jurusan->id }}" action="{{ route('admin.master.jurusan.destroy', $jurusan->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('delete-jurusan-{{ $jurusan->id }}', 'Jurusan {{ $jurusan->nama_jurusan }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Jurusan Modal -->
                                <div class="modal fade" id="editJurusanModal{{ $jurusan->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.master.jurusan.update', $jurusan->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Jurusan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Kode Jurusan *</label>
                                                        <input type="text" name="kode_jurusan" class="form-control" value="{{ $jurusan->kode_jurusan }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Jurusan *</label>
                                                        <input type="text" name="nama_jurusan" class="form-control" value="{{ $jurusan->nama_jurusan }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea name="deskripsi" class="form-control" rows="3">{{ $jurusan->deskripsi }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Tidak ada data jurusan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Kelas Table -->
    <div class="col-12 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Kelas</h6>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createKelasModal">
                    <i class="fas fa-plus me-1"></i>Tambah Kelas
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive table-scroll-10">
                    <table class="table table-bordered table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Nama Kelas</th>
                                <th width="35%">Jurusan</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $counter = 1;
                            @endphp
                            
                            @forelse($kelas as $k)
                                <tr>
                                    <td><strong>{{ $counter++ }}</strong></td>
                                    <td>
                                        {{ $k->tingkat }}-{{ $k->nama_kelas }}
                                    </td>
                                    <td>{{ $k->jurusan?->nama_jurusan ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editKelasModal{{ $k->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form id="delete-kelas-{{ $k->id }}" action="{{ route('admin.master.kelas.destroy', $k->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('delete-kelas-{{ $k->id }}', 'Kelas {{ $k->tingkat }}-{{ $k->nama_kelas }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Kelas Modal -->
                                <div class="modal fade" id="editKelasModal{{ $k->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.master.kelas.update', $k->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Kelas</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Kelas *</label>
                                                        <input type="text" name="nama_kelas" class="form-control" value="{{ $k->nama_kelas }}"
                                                               maxlength="2" pattern="[a-zA-Z0-9]{1,2}" placeholder="Maks. 2 karakter"
                                                               oninput="this.value=this.value.replace(/[^a-zA-Z0-9]/g,'').substring(0,2)" required>
                                                        <small class="text-muted">Huruf atau angka, maks. 2 karakter</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tingkat *</label>
                                                        <select name="tingkat" class="form-select" required>
                                                            <option value="X" {{ $k->tingkat == 'X' ? 'selected' : '' }}>X</option>
                                                            <option value="XI" {{ $k->tingkat == 'XI' ? 'selected' : '' }}>XI</option>
                                                            <option value="XII" {{ $k->tingkat == 'XII' ? 'selected' : '' }}>XII</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Jurusan *</label>
                                                        <select name="jurusan_id" class="form-select" required>
                                                            <option value="">-- Pilih Jurusan --</option>
                                                            @foreach($jurusans as $jur)
                                                                <option value="{{ $jur->id }}" {{ $k->jurusan_id == $jur->id ? 'selected' : '' }}>{{ $jur->nama_jurusan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Tidak ada data kelas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Baris Bawah: Guru Pembimbing dan Pengaturan -->
    <div class="col-12 col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Guru Pembimbing</h6>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createGuruModal">
                    <i class="fas fa-plus me-1"></i>Tambah Guru
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive table-scroll-10">
                    <table class="table table-bordered table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Nama Guru</th>
                                <th width="20%">NIP</th>
                                <th width="20%">No. HP</th>
                                <th width="15%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gurus as $index => $guru)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $guru->nama_guru }}</td>
                                    <td>{{ $guru->nip ?? '-' }}</td>
                                    <td>{{ $guru->no_hp ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $guru->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($guru->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editGuruModal{{ $guru->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form id="delete-guru-{{ $guru->id }}" action="{{ route('admin.master.guru.destroy', $guru->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('delete-guru-{{ $guru->id }}', 'Guru {{ $guru->nama_guru }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Guru Modal -->
                                <div class="modal fade" id="editGuruModal{{ $guru->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.master.guru.update', $guru->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Guru Pembimbing</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Lengkap Guru *</label>
                                                        <input type="text" name="nama_guru" class="form-control" value="{{ $guru->nama_guru }}" required pattern="[A-Za-z\s.,']+" oninput="this.value = this.value.replace(/[^A-Za-z\s.,']/g, '')">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">NIP</label>
                                                        <input type="text" name="nip" class="form-control" value="{{ $guru->nip ?: '-' }}" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">No. HP</label>
                                                        <input type="text" name="no_hp" class="form-control" value="{{ $guru->no_hp }}" inputmode="numeric" pattern="[0-9]*" maxlength="15" placeholder="62xxxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status *</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="active" {{ $guru->status == 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $guru->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Tidak ada data guru pembimbing</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Kolom Kanan: Pengaturan Presensi -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pengaturan Jarak Presensi</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.master.settings.update') }}" method="POST">
                    @csrf
                    <div class="alert alert-info small">
                        Atur batas jarak (dalam meter) agar siswa hanya bisa presensi jika berada di sekitar lokasi magang.
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-success fw-bold">Batas Zona Hijau (Maksimal)</label>
                        <div class="input-group">
                            <input type="number" name="radius_hijau" class="form-control" value="{{ $radiusHijau ?? 30 }}" required min="1">
                            <span class="input-group-text">meter</span>
                        </div>
                        <small class="text-muted">Aman untuk presensi (0 - {{ $radiusHijau ?? 30 }}m)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-warning fw-bold">Batas Zona Kuning (Maksimal)</label>
                        <div class="input-group">
                            <input type="number" name="radius_kuning" class="form-control" value="{{ $radiusKuning ?? 70 }}" required min="2">
                            <span class="input-group-text">meter</span>
                        </div>
                        <small class="text-muted">Peringatan tapi bisa presensi (> Zona Hijau s/d {{ $radiusKuning ?? 70 }}m)</small>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary w-100">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS FOR CREATING ================= -->

<!-- Create Jurusan Modal -->
<div class="modal fade" id="createJurusanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.master.jurusan.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jurusan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan *</label>
                        <input type="text" name="kode_jurusan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan *</label>
                        <input type="text" name="nama_jurusan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Create Kelas Modal -->
<div class="modal fade" id="createKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.master.kelas.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas *</label>
                        <input type="text" name="nama_kelas" class="form-control"
                               maxlength="2" pattern="[a-zA-Z0-9]{1,2}" placeholder="Maks. 2 karakter"
                               oninput="this.value=this.value.replace(/[^a-zA-Z0-9]/g,'').substring(0,2)" required>
                        <small class="text-muted">Huruf atau angka, maks. 2 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat *</label>
                        <select name="tingkat" class="form-select" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan *</label>
                        <select name="jurusan_id" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Create Guru Modal -->
<div class="modal fade" id="createGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.master.guru.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Guru Pembimbing Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap Guru *</label>
                        <input type="text" name="nama_guru" class="form-control" required pattern="[A-Za-z\s.,']+" oninput="this.value = this.value.replace(/[^A-Za-z\s.,']/g, '')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" inputmode="numeric" pattern="[0-9]*" maxlength="15" placeholder="62xxxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    function confirmDelete(formId, itemName) {
        Swal.fire({
            title: 'Hapus Data?',
            html: `Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?<br><small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Tindakan ini tidak dapat dibatalkan!</small>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: '<i class="fas fa-times me-1"></i> Tidak',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endpush
@endsection
