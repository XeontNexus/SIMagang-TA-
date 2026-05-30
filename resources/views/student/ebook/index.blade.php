@extends('layouts.app')

@section('title', 'E-Book - SIMagang')
@section('page-title', 'E-Book')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 bg-info text-white">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-book-reader me-2"></i>Logbook Kakak Kelas & Laporan Akhir</h6>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">
            <i class="fas fa-info-circle me-1"></i>
            Berikut adalah kumpulan logbook dan laporan akhir dari kakak kelas terdahulu yang sudah menyelesaikan magang.
        </p>

        @if($seniorStudents->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($seniorStudents as $student)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                @if($student->foto_profile)
                                    <img src="{{ asset('storage/'.$student->foto_profile) }}" class="rounded-circle" width="60" height="60" alt="{{ $student->nama_lengkap }}">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px">
                                        <i class="fas fa-user fa-2x"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 fw-bold">{{ $student->nama_lengkap }}</h6>
                                <small class="text-muted">{{ $student->kelas->nama_kelas ?? '-' }}</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-success">{{ $student->logbooks_count }} Logbook Approved</span>
                                <small class="text-muted">{{ $student->tanggal_selesai ? $student->tanggal_selesai->format('d M Y') : '-' }}</small>
                            </div>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-building me-1"></i>{{ $student->institusi ?? '-' }}
                            </p>
                        </div>
                        <a href="{{ route('student.ebook.detail', $student) }}" class="btn btn-info w-100">
                            <i class="fas fa-eye me-1"></i>Lihat Laporan Akhir
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $seniorStudents->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Laporan</h5>
            <p class="text-muted">Belum ada logbook atau laporan akhir dari kakak kelas yang tersedia.</p>
        </div>
        @endif
    </div>
</div>
@endsection
