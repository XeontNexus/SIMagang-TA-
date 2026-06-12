<ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.index') || request()->routeIs('admin.students.create') || request()->routeIs('admin.students.edit') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i>
            Kelola Akun Siswa
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.students.list') }}" class="nav-link {{ request()->routeIs('admin.students.list') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            Daftar List Siswa
        </a>
    </li>


    <!-- Data Master -->
    <li class="nav-item">
        <a href="{{ route('admin.master.index') }}" class="nav-link {{ request()->routeIs('admin.master.*') ? 'active' : '' }}">
            <i class="fas fa-database"></i>
            Kelola Data
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.location-requests.index') }}" class="nav-link {{ request()->routeIs('admin.location-requests.*') ? 'active' : '' }}">
            <i class="fas fa-map-pin"></i>
            Permintaan Lokasi
            @if(($pendingLocationRequests ?? 0) > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $pendingLocationRequests }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.presensi.report') }}" class="nav-link {{ request()->routeIs('admin.presensi.report') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            Laporan Presensi
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.logbooks.index') }}" class="nav-link {{ request()->routeIs('admin.logbooks.*') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            Laporan Logbook
        </a>
    </li>
    
    <li class="nav-item">
        <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            Profile
        </a>
    </li>
</ul>
