@extends('layouts.app')

@section('title', 'Permintaan Ubah Lokasi - SIMagang')
@section('page-title', 'Permintaan Ubah Lokasi Magang')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-map-marked-alt me-2"></i>Daftar Permintaan
            @if($pendingCount > 0)
                <span class="badge bg-warning text-dark ms-2">{{ $pendingCount }} menunggu</span>
            @endif
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Koordinat Lama</th>
                        <th>Koordinat Baru</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $index => $item)
                        <tr>
                            <td>{{ $requests->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $item->user->nama_lengkap }}</strong>
                                <div class="small text-muted">{{ $item->user->username }}</div>
                            </td>
                            <td class="small">
                                @if($item->old_latitude)
                                    {{ $item->old_latitude }}, {{ $item->old_longitude }}
                                    <br><a href="{{ $item->old_gmap_magang }}" target="_blank">Lihat map lama</a>
                                @else - @endif
                            </td>
                            <td class="small">
                                {{ $item->new_latitude }}, {{ $item->new_longitude }}
                                <br><a href="{{ $item->new_gmap_magang }}" target="_blank">Lihat map baru</a>
                            </td>
                            <td>
                                @if($item->status === 'pending')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @elseif($item->status === 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($item->status === 'pending')
                                    <form action="{{ route('admin.location-requests.approve', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.location-requests.reject', $item) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Permintaan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Alasan penolakan *</label>
                                                        <textarea name="admin_note" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <small class="text-muted">{{ $item->admin_note ?? '-' }}</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada permintaan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $requests->links() }}
    </div>
</div>
@endsection
