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
        <a href="#" class="nav-link" onclick="openAdminWhatsApp(event)">
            <i class="fas fa-whatsapp"></i>
            Hubungi Admin
        </a>
    </li>
</ul>

<script>
function openAdminWhatsApp(event) {
    event.preventDefault();
    
    @php
        $admin = \App\Models\User::where('role', 'admin')->whereNotNull('no_hp')->where('no_hp', '!=', '')->first();
        $adminPhone = $admin ? $admin->no_hp : env('ADMIN_CONTACT_PHONE', '081234567890');
        $user = auth()->user();
        $nama = $user->nama_lengkap ?? 'Siswa';
        $kelas = $user->kelas->nama_kelas ?? '-';
        $jurusan = $user->kelas->jurusan->nama_jurusan ?? '-';
        
        // Template pesan WhatsApp dengan nama, kelas, jurusan
        $message = "Halo Admin,\n\nSaya siswa yang ingin menanyakan sesuatu.\n\nData Saya:\nNama: {$nama}\nKelas: {$kelas}\nJurusan: {$jurusan}\n\nMasalah/Pertanyaan: [Silakan tuliskan pertanyaan or masalah Anda]\n\nTerima kasih.";
        $waLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $adminPhone) . '?text=' . urlencode($message);
    @endphp
    
    window.open("{{ $waLink }}", '_blank');
}
</script>
