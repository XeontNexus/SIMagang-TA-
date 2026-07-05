@php
    $badge = match($status) {
        'aktif'               => ['class' => 'success',  'label' => 'Aktif'],
        'proses'              => ['class' => 'warning',  'label' => 'Proses'],
        'menunggu'            => ['class' => 'secondary','label' => 'Menunggu'],
        'belum_dinotifikasi'  => ['class' => 'orange',   'label' => 'Belum Dinotifikasi'],
        'pending'             => ['class' => 'info',     'label' => 'Pending'],
        'inactive'            => ['class' => 'dark',     'label' => 'Non-Aktif'],
        'completed'           => ['class' => 'primary',  'label' => 'Selesai'],
        'rejected'            => ['class' => 'danger',   'label' => 'Ditolak'],
        'active'              => ['class' => 'success',  'label' => 'Aktif'],
        default               => ['class' => 'secondary','label' => ucfirst($status)],
    };
@endphp
@if($status === 'belum_dinotifikasi')
    <span class="badge" style="background-color:#fd7e14;color:#fff;">{{ $badge['label'] }}</span>
@else
    <span class="badge bg-{{ $badge['class'] }}">{{ $badge['label'] }}</span>
@endif
