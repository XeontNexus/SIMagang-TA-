@extends('layouts.app')

@section('title', 'Lengkapi Profil - SIMagang')
@section('page-title', 'Lengkapi Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Lengkapi Data Profil</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.complete.post') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP *</label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                               id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="institusi" class="form-label">Asal Sekolah *</label>
                        <input type="text" class="form-control @error('institusi') is-invalid @enderror" 
                               id="institusi" name="institusi" value="{{ old('institusi', $user->institusi ?? 'SMKN 1 Perhentian Raja') }}" required>
                        @error('institusi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jurusan_id" class="form-label">Jurusan *</label>
                            <select class="form-select @error('jurusan_id') is-invalid @enderror" id="jurusan_id" name="jurusan_id" required>
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $user->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                        {{ $jurusan->nama_jurusan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jurusan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kelas_id" class="form-label">Kelas *</label>
                            <select class="form-select @error('kelas_id') is-invalid @enderror" id="kelas_id" name="kelas_id" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id', $user->kelas_id) == $k->id ? 'selected' : '' }}>
                                        {{ $k->tingkat }} - {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai Magang *</label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                   id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $user->tanggal_mulai) }}" required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai Magang *</label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                   id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $user->tanggal_selesai) }}" required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="mitra_magang" class="form-label">Nama Perusahaan/Mitra *</label>
                        <input type="text" class="form-control @error('mitra_magang') is-invalid @enderror" 
                               id="mitra_magang" name="mitra_magang" value="{{ old('mitra_magang', $user->mitra_magang) }}" required>
                        @error('mitra_magang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat_magang" class="form-label">Alamat Magang *</label>
                        <textarea class="form-control @error('alamat_magang') is-invalid @enderror" id="alamat_magang" 
                                  name="alamat_magang" rows="2" required>{{ old('alamat_magang', $user->alamat_magang) }}</textarea>
                        @error('alamat_magang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pembimbing_lapangan" class="form-label">Nama Pembimbing Lapangan *</label>
                        <input type="text" class="form-control @error('pembimbing_lapangan') is-invalid @enderror" 
                               id="pembimbing_lapangan" name="pembimbing_lapangan" value="{{ old('pembimbing_lapangan', $user->pembimbing_lapangan) }}" required>
                        @error('pembimbing_lapangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_hp_pembimbing_lapangan" class="form-label">No. HP Pembimbing Lapangan *</label>
                        <input type="text" class="form-control @error('no_hp_pembimbing_lapangan') is-invalid @enderror" 
                               id="no_hp_pembimbing_lapangan" name="no_hp_pembimbing_lapangan" value="{{ old('no_hp_pembimbing_lapangan', $user->no_hp_pembimbing_lapangan) }}" required>
                        @error('no_hp_pembimbing_lapangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="guru_pembimbing_id" class="form-label">Guru Pembimbing *</label>
                        <select class="form-select @error('guru_pembimbing_id') is-invalid @enderror" 
                                id="guru_pembimbing_id" name="guru_pembimbing_id" required>
                            <option value="">Pilih Guru Pembimbing</option>
                            @foreach($guruPembimbings as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_pembimbing_id', $user->guru_pembimbing_id) == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->nama_guru }}
                                </option>
                            @endforeach
                        </select>
                        @error('guru_pembimbing_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_hp_guru_pembimbing" class="form-label">No. HP Guru Pembimbing *</label>
                        <input type="text" class="form-control @error('no_hp_guru_pembimbing') is-invalid @enderror" 
                               id="no_hp_guru_pembimbing" name="no_hp_guru_pembimbing" value="{{ old('no_hp_guru_pembimbing', $user->no_hp_guru_pembimbing) }}" required>
                        @error('no_hp_guru_pembimbing')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Simpan dan Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
