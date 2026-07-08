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
                    <label for="no_hp_display" class="form-label">Nomor WhatsApp (WA) <span class="text-danger">*</span></label>
                    {{-- Hidden input yang akan dikirim ke server (format 62xxx) --}}
                    <input type="hidden" id="no_hp" name="no_hp" value="{{ old('no_hp') }}">
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white fw-bold">+62</span>
                        <input type="text" class="form-control" id="no_hp_display"
                               placeholder="8xxxxxxxxxx" required
                               inputmode="numeric" maxlength="13" autocomplete="off"
                               value="{{ old('no_hp') ? ltrim(preg_replace('/^62/', '', old('no_hp')), '0') : '' }}">
                    </div>
                    <small class="text-muted">Isi angka setelah <strong>+62</strong> (tanpa angka 0 di depan), contoh: <strong>8123456789</strong></small>
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
        this.value = this.value.replace(/[^A-Za-zÀ-ÿ '.,]/g, '');
        this.setCustomValidity('');
    });
    namaInput.addEventListener('blur', function () {
        const val = this.value.trim();
        if (val && !/^[A-Za-zÀ-ÿ '.,]+$/.test(val)) {
            this.setCustomValidity("Nama hanya boleh berisi huruf, spasi, dan tanda apostrof (').");
            this.reportValidity();
        } else {
            this.setCustomValidity('');
        }
    });

    // Input +62 prefix - No WA
    const noHpHidden = document.getElementById('no_hp');      // hidden, dikirim ke server (62xxx)
    const noHpDisplay = document.getElementById('no_hp_display'); // visible, diisi user

    // Hanya izinkan angka, dan strip leading 0 jika user ketik 0 di depan
    noHpDisplay.addEventListener('input', function () {
        // Hanya angka
        this.value = this.value.replace(/[^0-9]/g, '');
        // Jika user ketik 0 di depan (sisa format lama), strip
        if (this.value.startsWith('0')) {
            this.value = this.value.replace(/^0+/, '');
        }
        // Maks 13 digit (setelah 62 = total 15)
        if (this.value.length > 13) {
            this.value = this.value.slice(0, 13);
        }
        this.setCustomValidity('');
        // Update hidden
        noHpHidden.value = this.value ? '62' + this.value : '';
    });

    noHpDisplay.addEventListener('blur', function () {
        const val = this.value;
        if (!val) return;
        if (val.length < 8) {
            this.setCustomValidity('Nomor WA terlalu pendek (min 8 digit setelah +62).');
            this.reportValidity();
        } else {
            this.setCustomValidity('');
        }
        noHpHidden.value = val ? '62' + val : '';
    });

    // Pastikan hidden value terisi saat load (jika ada old value)
    if (noHpDisplay.value) {
        noHpHidden.value = '62' + noHpDisplay.value;
    }

    // Validasi saat form submit
    document.querySelector('form').addEventListener('submit', function (e) {
        const nama = namaInput.value.trim();
        const noHpVal = noHpDisplay.value.trim();
        let valid = true;

        if (!nama) {
            namaInput.setCustomValidity('Nama lengkap wajib diisi.');
            namaInput.reportValidity();
            valid = false;
        } else {
            namaInput.setCustomValidity('');
        }

        if (!noHpVal) {
            noHpDisplay.setCustomValidity('Nomor WA wajib diisi.');
            noHpDisplay.reportValidity();
            valid = false;
        } else if (noHpVal.length < 8) {
            noHpDisplay.setCustomValidity('Nomor WA terlalu pendek.');
            noHpDisplay.reportValidity();
            valid = false;
        } else {
            noHpDisplay.setCustomValidity('');
            // Pastikan hidden sudah terisi
            noHpHidden.value = '62' + noHpVal;
        }

        if (!valid) e.preventDefault();
    });
</script>
@endpush
@endsection
