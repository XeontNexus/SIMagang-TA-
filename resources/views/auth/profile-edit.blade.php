@extends('layouts.app')

@section('title', 'Edit Profil - SIMagang')
@section('page-title', 'Edit Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Profil</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf

                    <h6 class="text-muted mb-3">Data Pribadi</h6>

                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                               id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', auth()->user()->nama_lengkap) }}" required pattern="[A-Za-z\s.,']+" oninput="this.value = this.value.replace(/[^A-Za-z\s.,']/g, '')">
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(auth()->user()->isAdmin())
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                               id="username" name="username" value="{{ old('username', auth()->user()->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                                   id="nisn" name="nisn" value="{{ old('nisn', auth()->user()->nisn) }}" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="text"
                                   class="form-control @error('no_hp') is-invalid @enderror"
                                   id="no_hp"
                                   name="no_hp"
                                   value="{{ old('no_hp', auth()->user()->no_hp) }}"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="16"
                                   placeholder="Contoh: 08123456789"
                                   oninput="normalizePhoneInput(this, 16)">
                            <small class="text-muted">Ketik 08xxx atau 628xxx — otomatis diformat</small>
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="institusi" class="form-label">Asal Sekolah</label>
                        <input type="text" class="form-control @error('institusi') is-invalid @enderror"
                               id="institusi" name="institusi" value="{{ old('institusi', auth()->user()->institusi ?? 'SMKN 1 Perhentian Raja') }}">
                        @error('institusi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(auth()->user()->isStudent())
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jurusan_id" class="form-label">Jurusan</label>
                            <select class="form-select @error('jurusan_id') is-invalid @enderror" id="jurusan_id" name="jurusan_id">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}" {{ old('jurusan_id', auth()->user()->jurusan_id) == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                @endforeach
                            </select>
                            @error('jurusan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select class="form-select @error('kelas_id') is-invalid @enderror" id="kelas_id" name="kelas_id">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id', auth()->user()->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">

                    <h6 class="text-muted mb-3">Data Magang Mitra</h6>

                    <div class="mb-3">
                        <label for="mitra_magang" class="form-label">Nama Perusahaan/Mitra</label>
                        <input type="text" class="form-control @error('mitra_magang') is-invalid @enderror"
                               id="mitra_magang" name="mitra_magang" value="{{ old('mitra_magang', auth()->user()->mitra_magang) }}">
                        @error('mitra_magang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat_magang" class="form-label">Alamat Magang</label>
                        <textarea class="form-control @error('alamat_magang') is-invalid @enderror"
                                  id="alamat_magang" name="alamat_magang" rows="3">{{ old('alamat_magang', auth()->user()->alamat_magang) }}</textarea>
                        @error('alamat_magang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        Link Google Maps lokasi magang diisi di menu <strong>Presensi</strong> sebelum absen masuk.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                   id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', auth()->user()->tanggal_mulai ? auth()->user()->tanggal_mulai->format('Y-m-d') : '') }}">
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                   id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', auth()->user()->tanggal_selesai ? auth()->user()->tanggal_selesai->format('Y-m-d') : '') }}">
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pembimbing_lapangan" class="form-label">Nama Pembimbing Lapangan</label>
                        <input type="text" class="form-control @error('pembimbing_lapangan') is-invalid @enderror"
                               id="pembimbing_lapangan" name="pembimbing_lapangan" value="{{ old('pembimbing_lapangan', auth()->user()->pembimbing_lapangan) }}" pattern="[A-Za-z\s.,']+" oninput="this.value = this.value.replace(/[^A-Za-z\s.,']/g, '')">
                        @error('pembimbing_lapangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_hp_pembimbing_lapangan" class="form-label">No. HP Pembimbing Lapangan</label>
                        <input type="text" class="form-control @error('no_hp_pembimbing_lapangan') is-invalid @enderror"
                               id="no_hp_pembimbing_lapangan"
                               name="no_hp_pembimbing_lapangan"
                               value="{{ old('no_hp_pembimbing_lapangan', auth()->user()->no_hp_pembimbing_lapangan) }}"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               maxlength="15"
                               placeholder="Contoh: 08123456789"
                               oninput="normalizePhoneInput(this, 15)">
                        <small class="text-muted">Ketik 08xxx atau 628xxx — otomatis diformat</small>
                        @error('no_hp_pembimbing_lapangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="guru_pembimbing_id" class="form-label">Guru Pembimbing</label>
                        <select class="form-select @error('guru_pembimbing_id') is-invalid @enderror"
                                id="guru_pembimbing_id" name="guru_pembimbing_id">
                            <option value="">Pilih Guru Pembimbing</option>
                            @foreach($guruPembimbings as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_pembimbing_id', auth()->user()->guru_pembimbing_id) == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->nama_guru }}
                                </option>
                            @endforeach
                        </select>
                        @error('guru_pembimbing_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_hp_guru_pembimbing" class="form-label">No. HP Guru Pembimbing</label>
                        <input type="text" class="form-control @error('no_hp_guru_pembimbing') is-invalid @enderror"
                               id="no_hp_guru_pembimbing"
                               name="no_hp_guru_pembimbing"
                               value="{{ old('no_hp_guru_pembimbing', auth()->user()->no_hp_guru_pembimbing) }}"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               maxlength="15"
                               placeholder="Contoh: 08123456789"
                               oninput="normalizePhoneInput(this, 15)">
                        <small class="text-muted">Otomatis diisi saat memilih guru pembimbing</small>
                        @error('no_hp_guru_pembimbing')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Normalize Indonesian phone number input:
     * - Strip non-digits
     * - Convert leading 0 → 62
     * - Enforce maxLength
     */
    function normalizePhoneInput(el, maxLen) {
        let val = el.value.replace(/[^0-9]/g, '');
        if (val.startsWith('0')) {
            val = '62' + val.substring(1);
        }
        el.value = val.slice(0, maxLen);
    }

    // Auto-fill guru pembimbing no_hp ketika guru dipilih
    const guruSelect = document.getElementById('guru_pembimbing_id');
    const noHpInput = document.getElementById('no_hp_guru_pembimbing');

    if (guruSelect) {
        guruSelect.addEventListener('change', async function() {
            if (this.value === '') {
                noHpInput.value = '';
                return;
            }

            try {
                const response = await fetch(`/api/guru-pembimbing/${this.value}/details`);
                const data = await response.json();

                if (response.ok && data.no_hp) {
                    noHpInput.value = data.no_hp;
                } else {
                    noHpInput.value = '';
                }
            } catch (error) {
                console.error('Error fetching guru details:', error);
                noHpInput.value = '';
            }
        });
    }
</script>
@endpush
