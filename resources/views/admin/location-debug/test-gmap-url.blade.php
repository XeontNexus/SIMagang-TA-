@extends('layouts.app')

@section('title', 'Debug: Test Google Maps URL')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>🔧 Debug: Test Google Maps URL Extraction</h1>
            <p class="text-muted">Admin tool untuk test dan debug Google Maps URL parsing</p>
        </div>
    </div>

    <div class="row">
        {{-- Test Input --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🧪 Test URL</h5>
                </div>
                <div class="card-body">
                    <form method="GET" id="testForm">
                        <div class="mb-3">
                            <label class="form-label">Google Maps URL:</label>
                            <textarea name="url" class="form-control font-monospace" rows="4" 
                                placeholder="Paste Google Maps URL di sini..."
                                value="{{ $testUrl }}">{{ $testUrl }}</textarea>
                            <small class="text-muted">Contoh: https://www.google.com/maps/place/Jakarta/@-6.175392,106.827153,17z</small>
                        </div>
                        <button type="submit" class="btn btn-primary">🔍 Test Extract</button>
                        <button type="reset" class="btn btn-secondary">Clear</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Result --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📊 Hasil Extract</h5>
                </div>
                <div class="card-body">
                    @if($testUrl)
                        @if($isValid)
                            <div class="alert alert-success">
                                ✅ <strong>Berhasil di-extract!</strong>
                            </div>
                            <dl class="row">
                                <dt class="col-sm-4">Latitude:</dt>
                                <dd class="col-sm-8"><code>{{ $result['latitude'] }}</code></dd>
                                
                                <dt class="col-sm-4">Longitude:</dt>
                                <dd class="col-sm-8"><code>{{ $result['longitude'] }}</code></dd>
                            </dl>
                            <div class="mt-3">
                                <a href="https://www.google.com/maps/place/@{{ $result['latitude'] }},{{ $result['longitude'] }},15z" 
                                   target="_blank" class="btn btn-sm btn-info">
                                    🗺️ Buka di Google Maps
                                </a>
                            </div>
                        @else
                            <div class="alert alert-danger">
                                ❌ <strong>Gagal di-extract!</strong>
                                <p class="mb-0">Link tidak valid atau format tidak didukung.</p>
                            </div>
                            <div class="mt-3">
                                <h6>Kemungkinan Penyebab:</h6>
                                <ul class="small">
                                    <li>Link dari sumber lain (bukan Google Maps)</li>
                                    <li>Link search page (tanpa lokasi spesifik)</li>
                                    <li>URL terpotong atau ada karakter aneh</li>
                                    <li>Format URL belum didukung</li>
                                </ul>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            ℹ️ Masukkan Google Maps URL di atas untuk test extraction
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Test Cases --}}
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📝 Test Cases (Format URL yang Didukung)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Format</th>
                                    <th>Contoh URL</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($testCases as $case)
                                    <tr>
                                        <td><strong>{{ $case['name'] }}</strong></td>
                                        <td><small class="font-monospace">{{ substr($case['url'], 0, 50) }}...</small></td>
                                        <td>
                                            <form method="GET" class="d-inline">
                                                <input type="hidden" name="url" value="{{ $case['url'] }}">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Test</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Information --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">ℹ️ Informasi Format URL</h5>
                </div>
                <div class="card-body">
                    <h6>Format yang Didukung:</h6>
                    <ul>
                        <li><strong>Query Parameter:</strong> <code>?q=lat,lon</code></li>
                        <li><strong>Place with @:</strong> <code>/@lat,lon</code> (Most Common)</li>
                        <li><strong>LL Parameter:</strong> <code>?ll=lat,lon</code></li>
                        <li><strong>Data Parameter:</strong> <code>!3dlat!4dlon</code></li>
                        <li><strong>Short URLs:</strong> <code>maps.app.goo.gl/xxxxx</code> (auto-expanded)</li>
                    </ul>

                    <hr>

                    <h6>Validasi Koordinat:</h6>
                    <ul>
                        <li>Latitude: -90 hingga +90</li>
                        <li>Longitude: -180 hingga +180</li>
                    </ul>

                    <hr>

                    <h6>Notes:</h6>
                    <ul>
                        <li>URL akan di-decode (URL-encoded characters di-convert)</li>
                        <li>Short URLs akan di-expand menggunakan cURL</li>
                        <li>Koordinat harus valid (bukan di luar range)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    code {
        background-color: #f5f5f5;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
    }
    
    .font-monospace {
        font-family: 'Courier New', monospace;
    }
</style>
@endsection
