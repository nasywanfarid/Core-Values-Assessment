@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card premium-card border-0">
            <div class="card-body p-3 p-md-4 d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(236, 72, 153, 0.1);">
                    <i class="fas fa-users fs-4" style="color: #ec4899;"></i>
                </div>
                <div class="ms-2 ms-md-3 overflow-hidden">
                    <h6 class="text-muted mb-0 small text-truncate">Karyawan</h6>
                    <h4 class="mb-0 fw-bold">{{ $totalKaryawan }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card premium-card border-0">
            <div class="card-body p-3 p-md-4 d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(25, 135, 84, 0.1);">
                    <i class="fas fa-check-circle fs-4 text-success"></i>
                </div>
                <div class="ms-2 ms-md-3 overflow-hidden">
                    <h6 class="text-muted mb-0 small text-truncate">Selesai</h6>
                    <h4 class="mb-0 fw-bold">{{ $completedAssignments }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card premium-card border-0">
            <div class="card-body p-3 p-md-4 d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(255, 193, 7, 0.1);">
                    <i class="fas fa-hourglass-half fs-4 text-warning"></i>
                </div>
                <div class="ms-2 ms-md-3 overflow-hidden">
                    <h6 class="text-muted mb-0 small text-truncate">Pending</h6>
                    <h4 class="mb-0 fw-bold">{{ $pendingAssignments }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card premium-card border-0">
            <div class="card-body p-3 p-md-4 d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(13, 202, 240, 0.1);">
                    <i class="fas fa-sitemap fs-4 text-info"></i>
                </div>
                <div class="ms-2 ms-md-3 overflow-hidden">
                    <h6 class="text-muted mb-0 small text-truncate">Divisi</h6>
                    <h4 class="mb-0 fw-bold">{{ $totalDivisi }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Ringkasan Hasil Penilaian (Grade) -->
    <div class="col-12 col-xl-6">
        <div class="card premium-card border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Hasil Penilaian</h5>
                </div>

                @if($latestPeriodDate && !empty($employeeResults))
                    @php
                        $groupedByGrade = collect($employeeResults)->groupBy('grade');
                        $gradeColors = [
                            'A' => '#10b981', 'B' => '#3b82f6', 'C' => '#f59e0b', 'D' => '#f97316', 'E' => '#ef4444'
                        ];
                    @endphp

                    <div id="gradeGroup">
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $grade)
                            @php 
                                $emps = $groupedByGrade->get($grade, collect());
                                $count = $emps->count();
                            @endphp
                            <div class="mb-2">
                                <div class="d-grid">
                                    <button class="btn btn-light bg-opacity-50 text-start d-flex justify-content-between align-items-center py-2 px-3 border-0 rounded-3 shadow-none" 
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGrade{{ $grade }}" aria-expanded="false">
                                        <span>
                                            <span class="badge rounded-pill me-2" style="background-color: {{ $gradeColors[$grade] }};">Grade {{ $grade }}</span>
                                            <small class="text-muted">{{ $count }} Karyawan</small>
                                        </span>
                                        <i class="fas fa-chevron-down small opacity-50"></i>
                                    </button>
                                </div>
                                <div id="collapseGrade{{ $grade }}" class="collapse mt-2">
                                    <div class="p-3 bg-light bg-opacity-25 rounded-3 border">
                                        @if($count > 0)
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($emps as $emp)
                                                    <span class="badge bg-white text-dark border fw-normal">{{ $emp->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <small class="text-muted italic">Tidak ada karyawan di grade ini.</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-5 text-center text-muted">
                        <i class="fas fa-clipboard-list fs-1 mb-3 opacity-25"></i>
                        <p>Belum ada data penilaian.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ringkasan Kelompok Karyawan (K-Means) -->
    <div class="col-12 col-xl-6">
        <div class="card premium-card border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Kelompok Karyawan</h5>
                </div>

                @if($latestPeriodDate && !empty($employeeResults))
                    @php
                        $groupedByCluster = collect($employeeResults)->groupBy('kategori');
                        $clusterInfo = [
                            'Implementasi Core Values Tinggi' => ['color' => '#10b981', 'icon' => 'fa-smile-beam'],
                            'Implementasi Core Values Rendah' => ['color' => '#ef4444', 'icon' => 'fa-frown-open']
                        ];
                    @endphp

                    <div id="clusterGroup">
                        @foreach(['Implementasi Core Values Tinggi', 'Implementasi Core Values Rendah'] as $kategori)
                            @php 
                                $emps = $groupedByCluster->get($kategori, collect());
                                $count = $emps->count();
                            @endphp
                            <div class="mb-2">
                                <div class="d-grid">
                                    <button class="btn btn-light bg-opacity-50 text-start d-flex justify-content-between align-items-center py-2 px-3 border-0 rounded-3 shadow-none" 
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseCluster{{ Str::slug($kategori) }}" aria-expanded="false">
                                        <span>
                                            <i class="fas {{ $clusterInfo[$kategori]['icon'] }} me-2" style="color: {{ $clusterInfo[$kategori]['color'] }};"></i>
                                            <span class="fw-bold" style="color: {{ $clusterInfo[$kategori]['color'] }};">{{ $kategori }}</span>
                                            <small class="text-muted ms-2">{{ $count }} Karyawan</small>
                                        </span>
                                        <i class="fas fa-chevron-down small opacity-50"></i>
                                    </button>
                                </div>
                                <div id="collapseCluster{{ Str::slug($kategori) }}" class="collapse mt-2">
                                    <div class="p-3 bg-light bg-opacity-25 rounded-3 border">
                                        @if($count > 0)
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($emps as $emp)
                                                    <span class="badge bg-white text-dark border fw-normal">{{ $emp->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <small class="text-muted italic">Tidak ada karyawan di kelompok ini.</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-5 text-center text-muted">
                        <i class="fas fa-layer-group fs-1 mb-3 opacity-25"></i>
                        <p>Analisis K-Means belum tersedia.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
