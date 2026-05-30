@extends('layouts.app')

@section('title', 'Tambah Logbook - SIMagang')
@section('page-title', 'Tambah Logbook')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Form Logbook</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('student.logbooks.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="minggu_ke" class="form-label">Minggu Ke *</label>
                            <select class="form-select @error('minggu_ke') is-invalid @enderror" id="minggu_ke" name="minggu_ke" required>
                                <option value="">-- Pilih Minggu --</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('minggu_ke') == $i ? 'selected' : '' }}>Minggu Ke-{{ $i }}</option>
                                @endfor
                            </select>
                            @error('minggu_ke')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bulan" class="form-label">Bulan *</label>
                            <select class="form-select @error('bulan') is-invalid @enderror" id="bulan" name="bulan" required>
                                <option value="">-- Pilih Bulan --</option>
                                @php
                                    $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                @endphp
                                @foreach($bulanList as $index => $namaBulan)
                                    <option value="{{ $index + 1 }}" {{ old('bulan', now()->month) == ($index + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                            @error('bulan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <input type="text" class="form-control bg-light" id="tahun" value="{{ now()->year }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="kegiatan" class="form-label">Kegiatan *</label>
                        <textarea class="form-control @error('kegiatan') is-invalid @enderror" id="kegiatan" 
                                  name="kegiatan" rows="3" required>{{ old('kegiatan') }}</textarea>
                        @error('kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <label for="hasil" class="form-label">Hasil</label>
                        <textarea class="form-control @error('hasil') is-invalid @enderror" id="hasil" 
                                  name="hasil" rows="2">{{ old('hasil') }}</textarea>
                        @error('hasil')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kendala" class="form-label">Kendala</label>
                        <textarea class="form-control @error('kendala') is-invalid @enderror" id="kendala" 
                                  name="kendala" rows="2">{{ old('kendala') }}</textarea>
                        @error('kendala')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="solusi" class="form-label">Solusi</label>
                        <textarea class="form-control @error('solusi') is-invalid @enderror" id="solusi" 
                                  name="solusi" rows="2">{{ old('solusi') }}</textarea>
                        @error('solusi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Submit Logbook
                        </button>
                        <a href="{{ route('student.logbooks.index') }}" class="btn btn-outline-secondary">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
