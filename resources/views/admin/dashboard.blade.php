@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card premium-card border-0">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: rgba(236, 72, 153, 0.1);">
                    <i class="fas fa-users fs-3" style="color: #ec4899;"></i>
                </div>
                <div class="ms-3">
                    <h6 class="text-muted mb-1">Total Karyawan</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalKaryawan }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card border-0">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                    <i class="fas fa-check-circle fs-3 text-success"></i>
                </div>
                <div class="ms-3">
                    <h6 class="text-muted mb-1">Penilaian Selesai</h6>
                    <h3 class="mb-0 fw-bold">{{ $completedAssignments }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card border-0">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                    <i class="fas fa-hourglass-half fs-3 text-warning"></i>
                </div>
                <div class="ms-3">
                    <h6 class="text-muted mb-1">Menunggu Penilaian</h6>
                    <h3 class="mb-0 fw-bold">{{ $pendingAssignments }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card border-0">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: rgba(13, 202, 240, 0.1);">
                    <i class="fas fa-sitemap fs-3 text-info"></i>
                </div>
                <div class="ms-3">
                    <h6 class="text-muted mb-1">Total Divisi</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalDivisi }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card premium-card border-0 p-2 h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Statistik Penilaian</h5>
                <div class="py-5 text-center text-muted">
                    <i class="fas fa-chart-line fs-1 mb-3"></i>
                    <p>Selamat datang di Dashboard Core Values System. Pilih menu di samping untuk mengelola data atau melihat hasil penilaian.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card premium-card border-0 p-2 h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Aktivitas Terbaru</h5>
                <div class="d-flex mb-3 align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                        <i class="fas fa-bell text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0"></h6>
                        <small class="text-muted">2 jam yang lalu</small>
                    </div>
                </div>
                <div class="d-flex mb-3 align-items-center">
                    <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                        <i class="fas fa-user-plus text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Karyawan baru terdaftar</h6>
                        <small class="text-muted">5 jam yang lalu</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
