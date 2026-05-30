@extends('layouts.app')

@section('title', 'Persetujuan Pendaftaran - SIMagang')
@section('page-title', 'Persetujuan Pendaftaran Siswa')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Siswa Menunggu Persetujuan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>No. WhatsApp</th>
                        <th>Institusi</th>
                        <th>Jurusan</th>
                        <th>Guru Pembimbing</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->nama_lengkap }}</td>
                            <td>{{ $student->username }}</td>
                            <td>{{ $student->no_hp ?? '-' }}</td>
                            <td>{{ $student->institusi }}</td>
                            <td>{{ $student->jurusan?->nama_jurusan ?? '-' }}</td>
                            <td>{{ $student->guruPembimbing?->nama_guru ?? '-' }}</td>
                            <td>{{ $student->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <form action="{{ route('admin.students.approve', $student) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.students.reject', $student) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Tidak ada siswa yang menunggu persetujuan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $students->links() }}
    </div>
</div>
@endsection
