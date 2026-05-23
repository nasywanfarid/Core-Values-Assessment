@extends('layouts.admin')

@section('title', 'Kelompok Karyawan (K-Means Clustering)')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.results.clustering') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Pilih Periode</label>
                        <select name="date" class="form-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            @foreach($periods as $p)
                                <option value="{{ $p->assessment_date }}" {{ $selectedDate == $p->assessment_date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($p->assessment_date)->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Pilih Cabang</label>
                        <select name="branch_id" class="form-select" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sync-alt me-2"></i> Analisis Clustering
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($results)
    <div class="row mb-4">
        <!-- Summary Cards -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                <div class="card-body bg-success text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Kategori</h6>
                            <h3 class="fw-bold mb-0">Tinggi</h3>
                        </div>
                        <div class="display-6 fw-bold">{{ $summary['Implementasi Core Values Tinggi'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                <div class="card-body bg-danger text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Kategori</h6>
                            <h3 class="fw-bold mb-0">Rendah</h3>
                        </div>
                        <div class="display-6 fw-bold">{{ $summary['Implementasi Core Values Rendah'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @php $categories = ['Implementasi Core Values Tinggi', 'Implementasi Core Values Rendah']; @endphp
        
        @foreach($categories as $cat)
        <div class="col-lg-6 mb-4">
            <div class="card premium-card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 d-flex align-items-center">
                        <span class="badge {{ $cat == 'Implementasi Core Values Tinggi' ? 'bg-success' : 'bg-danger' }} rounded-circle p-2 me-2" style="width: 12px; height: 12px;"> </span>
                        Daftar {{ $cat }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @if(isset($results[$cat]))
                            @foreach($results[$cat] as $idx => $emp)
                            <div class="list-group-item border-0 py-3 px-4 hover-bg">
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold text-muted me-3" style="width: 25px;">{{ $idx + 1 }}.</div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark">{{ $emp->name }}</div>
                                        <div class="small text-muted">{{ $emp->division }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-light text-primary rounded-pill px-3">{{ $emp->total_score }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-user-slash d-block mb-2 fs-3 opacity-25"></i>
                                Tidak ada karyawan di kategori ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="row">
        <div class="col-12 text-center py-5">
            <div class="premium-card p-5 bg-white d-inline-block rounded-4 shadow-sm">
                <i class="fas fa-layer-group text-primary mb-3" style="font-size: 4rem;"></i>
                <h4 class="fw-bold">Analisis Kelompok Karyawan</h4>
                <p class="text-muted">Silakan pilih periode dan cabang untuk memulai analisis clustering K-Means.</p>
            </div>
        </div>
    </div>
@endif
@endsection
