@extends('layouts.admin')

@section('title', 'Data Indikator Core Values')

@section('content')
<div class="card premium-card border-0 mb-4">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1">Manajemen Indikator</h5>
            <p class="text-muted mb-0">Kelola indikator Core Values dan deskripsi skala penilaiannya.</p>
        </div>
        <a href="{{ route('admin.indicators.create') }}" class="btn btn-primary rounded px-4">
            <i class="fas fa-plus me-2"></i> Indikator
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    @foreach($indicators as $indicator)
    <div class="col-md-6 mb-4">
        <div class="card premium-card border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-primary mb-2">Indikator</span>
                        <h5 class="fw-bold text-dark">{{ $indicator->name }}</h5>
                    </div>
                    <div class="d-flex gap-2 flex-shrink-0">
                        <a href="{{ route('admin.indicators.edit', $indicator->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 d-inline-flex align-items-center justify-content-center" style="height: 32px;">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @if(auth()->user()->role !== 'hr')
                        <form action="{{ route('admin.indicators.destroy', $indicator->id) }}" method="POST" id="delete-form-{{ $indicator->id }}" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 d-inline-flex align-items-center justify-content-center" onclick="confirmDelete('delete-form-{{ $indicator->id }}', 'Indikator ini akan dihapus secara permanen!')" style="height: 32px;">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <p class="text-muted small mb-4">{{ $indicator->description }}</p>
                
                <div class="scales-list">
                    <div class="d-flex mb-2 align-items-start">
                        <span class="badge bg-danger me-2 mt-1">1</span>
                        <span class="small text-dark">{{ $indicator->scale_1 }}</span>
                    </div>
                    <div class="d-flex mb-2 align-items-start">
                        <span class="badge bg-warning text-dark me-2 mt-1">2</span>
                        <span class="small text-dark">{{ $indicator->scale_2 }}</span>
                    </div>
                    <div class="d-flex mb-2 align-items-start">
                        <span class="badge bg-info text-dark me-2 mt-1">3</span>
                        <span class="small text-dark">{{ $indicator->scale_3 }}</span>
                    </div>
                    <div class="d-flex mb-2 align-items-start">
                        <span class="badge bg-primary me-2 mt-1">4</span>
                        <span class="small text-dark">{{ $indicator->scale_4 }}</span>
                    </div>
                    <div class="d-flex mb-0 align-items-start">
                        <span class="badge bg-success me-2 mt-1">5</span>
                        <span class="small text-dark">{{ $indicator->scale_5 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
