@extends('layouts.app')

@section('page-title', 'Notifikasi')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bell me-2"></i> Semua Notifikasi
                </h5>
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm">
                            <i class="fas fa-check-double me-1"></i>Sudah lihat semua
                        </button>
                    </form>
                @endif
            </div>
            <div class="card-body">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="notification-item p-3 border-bottom {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas {{ $notification->icon ?? 'fa-bell' }} fa-lg text-{{ $notification->type }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 {{ $notification->read_at ? 'text-muted' : '' }}">
                                        {{ $notification->title }}
                                    </h6>
                                    <p class="mb-1 text-muted small">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="ms-2">
                                    @if($notification->link)
                                        <a href="{{ $notification->link }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p class="mb-0">Tidak ada notifikasi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .notification-item {
        transition: all 0.3s ease;
    }

    .notification-item:hover {
        background-color: #f8f9fa !important;
    }

    .text-success {
        color: #28a745;
    }

    .text-info {
        color: #17a2b8;
    }

    .text-warning {
        color: #ffc107;
    }

    .text-danger {
        color: #dc3545;
    }
</style>
@endsection
