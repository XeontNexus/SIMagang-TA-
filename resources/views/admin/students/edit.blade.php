@extends('layouts.app')

@section('title', 'Edit Siswa - SIMagang')
@section('page-title', 'Edit Siswa')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Edit Akun Siswa: {{ $student->nama_lengkap }}</h6>
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

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-1"></i>
            Data institusi & magang diisi oleh siswa melalui menu <strong>Profile</strong> di akun mereka.
            Status diperbarui otomatis: <em>Menunggu</em> → <em>Proses</em> → <em>Aktif</em>.
        </div>

        <div class="mb-4">
            <label class="form-label text-muted small mb-1">Status Saat Ini</label>
            <div>@include('partials.student-status-badge', ['status' => $student->status])</div>
        </div>

        <form action="{{ route('admin.students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-12">
                    <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>Data Akun</h6><hr>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                           value="{{ old('nama_lengkap', $student->nama_lengkap) }}" required autocomplete="off">
                    <small class="text-muted">Hanya huruf, spasi, dan tanda apostrof (')</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $student->username) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label">Nomor WhatsApp (WA) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp"
                           value="{{ old('no_hp', $student->no_hp) }}" placeholder="08xxxxxxxxxx" required
                           inputmode="numeric" maxlength="13" autocomplete="off">
                    <small class="text-muted">Hanya angka, dimulai <strong>08</strong>, minimal <strong>10</strong> dan maksimal <strong>13 digit</strong></small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
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

    // Validasi Nama Lengkap
    const namaInput = document.getElementById('nama_lengkap');
    namaInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^A-Za-z ']/g, '');
        this.setCustomValidity('');
    });
    namaInput.addEventListener('blur', function () {
        const val = this.value.trim();
        if (val && !/^[A-Za-z ']+$/.test(val)) {
            this.setCustomValidity("Nama hanya boleh berisi huruf, spasi, dan apostrof (').");
            this.reportValidity();
        } else {
            this.setCustomValidity('');
        }
    });

    // Validasi No HP
    const noHpInput = document.getElementById('no_hp');
    noHpInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 13) this.value = this.value.slice(0, 13);
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

    // Validasi saat submit
    document.querySelector('form').addEventListener('submit', function (e) {
        const nama = namaInput.value.trim();
        const noHp = noHpInput.value;
        let valid = true;
        if (!nama || !/^[A-Za-z ']+$/.test(nama)) {
            namaInput.setCustomValidity("Nama hanya boleh berisi huruf, spasi, dan apostrof (').");
            namaInput.reportValidity();
            valid = false;
        } else {
            namaInput.setCustomValidity('');
        }
        if (!noHp || !noHp.startsWith('08') || noHp.length < 10 || noHp.length > 13) {
            noHpInput.setCustomValidity(!noHp ? 'Nomor WA wajib diisi.' :
                !noHp.startsWith('08') ? 'Nomor WA harus dimulai dengan 08.' :
                noHp.length < 10 ? 'Nomor WA minimal 10 digit.' : 'Nomor WA maksimal 13 digit.');
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
