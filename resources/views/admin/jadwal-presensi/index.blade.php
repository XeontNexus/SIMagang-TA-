@extends('layouts.app')

@section('title', 'Pengaturan Jadwal - SIMagang')
@section('page-title', 'Pengaturan Jadwal Presensi')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Jadwal Presensi Siswa</h6>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <form method="POST" action="{{ route('admin.jadwal-presensi.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <select name="user_id" class="form-select" required>
                            <option value="">Pilih Siswa</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="hari" class="form-select" required>
                            <option value="">Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="time" name="jam_masuk" class="form-control" placeholder="Jam Masuk" required>
                    </div>
                    <div class="col-md-2">
                        <input type="time" name="jam_keluar" class="form-control" placeholder="Jam Keluar" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Jadwal
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Hari</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $index => $jadwal)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $jadwal->user->nama_lengkap }}</td>
                            <td>{{ $jadwal->hari }}</td>
                            <td>{{ $jadwal->jam_masuk }}</td>
                            <td>{{ $jadwal->jam_keluar }}</td>
                            <td>
                                <form action="{{ route('admin.jadwal-presensi.destroy', $jadwal) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada jadwal</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
