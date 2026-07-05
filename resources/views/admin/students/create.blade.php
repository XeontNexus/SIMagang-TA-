@extends('layouts.app')

@section('title', 'Tambah Siswa - SIMagang')
@section('page-title', 'Tambah Siswa')

@section('content')
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fab fa-whatsapp me-2"></i>
    <strong>ℹ️ Notifikasi WhatsApp:</strong> Setelah siswa ditambahkan, Anda dapat mengirimkan detail login siswa secara manual melalui WhatsApp pada tombol aksi di daftar siswa.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Siswa</h6>
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.students.store') }}" method="POST">
            @csrf

            <div class="row">
                {{-- Data Akun --}}
                <div class="col-12">
                    <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>Data Akun</h6>
                    <hr>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                           value="{{ old('nama_lengkap') }}" required
                           autocomplete="off">
                    <small class="text-muted">Hanya huruf, spasi, dan tanda apostrof (') yang diperbolehkan</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="{{ old('username') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label">Nomor WhatsApp (WA) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp"
                           value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" required
                           inputmode="numeric" maxlength="13" autocomplete="off">
                    <small class="text-muted">Hanya angka, dimulai dari <strong>08</strong>, minimal <strong>10</strong> dan maksimal <strong>13 digit</strong></small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <small class="text-muted">
                        Minimal 6 karakter<br>
                        💡 <strong>Kirim info login ke siswa secara manual via WhatsApp setelah akun dibuat</strong>
                    </small>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Validasi Nama Lengkap: hanya huruf, spasi, dan apostrof (')
    const namaInput = document.getElementById('nama_lengkap');
    namaInput.addEventListener('input', function () {
        // Hapus karakter selain huruf, spasi, apostrof
        this.value = this.value.replace(/[^A-Za-z ']/g, '');
        this.setCustomValidity('');
    });
    namaInput.addEventListener('blur', function () {
        const val = this.value.trim();
        if (val && !/^[A-Za-z ']+$/.test(val)) {
            this.setCustomValidity("Nama hanya boleh berisi huruf, spasi, dan tanda apostrof (').");
            this.reportValidity();
        } else {
            this.setCustomValidity('');
        }
    });

    // Validasi No HP: hanya angka, dimulai 08, min 10 max 13 digit
    const noHpInput = document.getElementById('no_hp');
    noHpInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 13) {
            this.value = this.value.slice(0, 13);
        }
        this.setCustomValidity('');
    });
    noHpInput.addEventListener('blur', function () {
        const val = this.value;
        if (!val) return;
        if (!val.startsWith('08')) {
            this.setCustomValidity('Nomor WA harus dimulai dengan 08.');
            this.reportValidity();
        } else if (val.length < 10) {
            this.setCustomValidity('Nomor WA minimal 10 digit.');
            this.reportValidity();
        } else if (val.length > 13) {
            this.setCustomValidity('Nomor WA maksimal 13 digit.');
            this.reportValidity();
        } else {
            this.setCustomValidity('');
        }
    });

    // Validasi saat form submit
    document.querySelector('form').addEventListener('submit', function (e) {
        const nama = namaInput.value.trim();
        const noHp = noHpInput.value;
        let valid = true;

        if (!nama) {
            namaInput.setCustomValidity('Nama lengkap wajib diisi.');
            namaInput.reportValidity();
            valid = false;
        } else if (!/^[A-Za-z ']+$/.test(nama)) {
            namaInput.setCustomValidity("Nama hanya boleh berisi huruf, spasi, dan tanda apostrof (').");
            namaInput.reportValidity();
            valid = false;
        } else {
            namaInput.setCustomValidity('');
        }

        if (!noHp) {
            noHpInput.setCustomValidity('Nomor WA wajib diisi.');
            noHpInput.reportValidity();
            valid = false;
        } else if (!noHp.startsWith('08')) {
            noHpInput.setCustomValidity('Nomor WA harus dimulai dengan 08.');
            noHpInput.reportValidity();
            valid = false;
        } else if (noHp.length < 10) {
            noHpInput.setCustomValidity('Nomor WA minimal 10 digit.');
            noHpInput.reportValidity();
            valid = false;
        } else if (noHp.length > 13) {
            noHpInput.setCustomValidity('Nomor WA maksimal 13 digit.');
            noHpInput.reportValidity();
            valid = false;
        } else {
            noHpInput.setCustomValidity('');
        }

        if (!valid) e.preventDefault();
    });
</script>
@endpush
@endsection
