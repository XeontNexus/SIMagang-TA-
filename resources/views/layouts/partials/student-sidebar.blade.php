<ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
        <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </a>
    </li>
    
    <li class="nav-item">
        <a href="{{ route('student.presensi.index') }}" class="nav-link {{ request()->routeIs('student.presensi.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-check"></i>
            Presensi
        </a>
    </li>
    
    <li class="nav-item">
        <a href="{{ route('student.logbooks.index') }}" class="nav-link {{ request()->routeIs('student.logbooks.*') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            Logbook
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            Profile
        </a>
    </li>

    <li class="nav-item">
        @if(!empty($adminContactPhone))
            <a href="#" class="nav-link" onclick="openAdminWhatsApp(event)">
                <i class="fas fa-phone"></i>
                Hubungi Admin
            </a>
        @else
            <span class="nav-link text-white-50" title="Admin belum mengisi nomor HP di profil">
                <i class="fas fa-phone"></i>
                Hubungi Admin
                <small class="d-block ps-4" style="font-size:0.7rem;">No. admin belum diatur</small>
            </span>
        @endif
    </li>
</ul>

@if(!empty($adminContactPhone))
<script>
function openAdminWhatsApp(event) {
    event.preventDefault();

    @php
        $user = auth()->user();
        $nama = $user->nama_lengkap ?? 'Siswa';
        $kelasLabel = $user->kelas
            ? trim(($user->kelas->tingkat ?? '') . ' ' . ($user->kelas->nama_kelas ?? ''))
            : ($user->kelas ?? '-');
        $jurusanLabel = $user->jurusan?->nama_jurusan ?? $user->jurusan ?? '-';
        $message = "Halo Admin,\n\nSaya siswa yang ingin menanyakan sesuatu.\n\nData Saya:\nNama: {$nama}\nKelas: {$kelasLabel}\nJurusan: {$jurusanLabel}\n\nMasalah/Pertanyaan: [Silakan tuliskan pertanyaan atau masalah Anda]\n\nTerima kasih.";
        $waLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $adminContactPhone) . '?text=' . urlencode($message);
    @endphp

    window.open(@json($waLink), '_blank');
}
</script>
@endif
