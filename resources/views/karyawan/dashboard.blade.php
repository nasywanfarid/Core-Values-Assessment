@extends('layouts.admin')

@section('title', 'Tugas Penilaian (To-Do List)')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4">
    <div class="col-md-8">
        <div class="card premium-card border-0 mb-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-clipboard-list text-warning me-2"></i> Penilaian Yang Belum Dilakukan</h5>
                
                @if($pendingAssignments->isEmpty())
                <div class="text-center py-5">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-check-double text-success px-3 fs-1"></i>
                    </div>
                    <h5 class="text-muted">Luar Biasa!</h5>
                    <p class="text-muted">Anda telah menyelesaikan semua tugas penilaian saat ini.</p>
                </div>
                @else
                <div class="list-group list-group-flush border-top">
                    @foreach($pendingAssignments as $assignment)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom px-0">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ $assignment->reviewee->name }}&background=f1f3f5&color=495057" class="rounded-circle me-3" width="48" height="48">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $assignment->reviewee->name }}</h6>
                                <div class="mb-1 small">
                                    <span class="badge bg-warning text-dark"><i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($assignment->assessment_date)->translatedFormat('d F Y') }}</span>
                                </div>
                                <small class="text-muted"><i class="fas fa-id-badge me-1"></i> NIP: {{ $assignment->reviewee->nip ?? '-' }} | Divisi: {{ $assignment->reviewee->division->name ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('karyawan.evaluate', $assignment->id) }}" class="btn btn-primary btn-sm px-4 rounded-pill">
                            Mulai Penilaian <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card premium-card border-0 bg-success text-white mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-white-50 text-uppercase fw-bold mb-0">Progress Bulanan</h6>
                    <i class="fas fa-chart-line fs-3 text-white-50"></i>
                </div>
                
                @php
                    $total = $pendingAssignments->count() + $completedAssignments->count();
                    $percent = $total > 0 ? round(($completedAssignments->count() / $total) * 100) : 100;
                @endphp
                
                <h2 class="display-4 fw-bold mb-0">{{ $percent }}%</h2>
                <p class="mb-4 text-white-50">Tugas selesai ({{ $completedAssignments->count() }}/{{ $total }})</p>
                
                <div class="progress" style="height: 8px; background-color: rgba(255,255,255,0.2);">
                    <div class="progress-bar bg-white" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        
        <div class="card premium-card border-0">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-history text-muted me-2"></i> Riwayat Penilaian Tersimpan</h6>
                
                @if($completedAssignments->isEmpty())
                <p class="text-muted small text-center my-4">Belum ada riwayat.</p>
                @else
                <div class="list-group list-group-flush border-top">
                    @foreach($completedAssignments->take(5) as $completed)
                    <div class="list-group-item px-0 py-2 border-bottom d-flex align-items-center">
                        <i class="fas fa-circle text-success me-2" style="font-size: 8px;"></i>
                        <span class="small fw-medium">{{ $completed->reviewee->name }}</span>
                        <span class="ms-auto text-muted small">{{ $completed->updated_at->format('d/m/Y') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
