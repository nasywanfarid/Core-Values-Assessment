@extends('layouts.admin')

@section('title', 'Detail Penilaian - ' . $employee->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.results.period', ['date' => $date, 'branch' => $employee->branch_id]) }}" class="btn btn-outline-secondary btn-sm rounded-circle me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=ec4899&color=fff" 
                             class="rounded-circle me-3 border border-2 border-primary p-1" width="60" height="60">
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">{{ $employee->name }}</h4>
                            <p class="text-muted mb-0">{{ $employee->division->name ?? '-' }} | {{ $employee->branch->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Table -->
    <div class="col-lg-12 mb-4">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold text-primary">Rincian Nilai per Penilai</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle text-start ps-4">Nama Penilai</th>
                                <th colspan="8">Indikator</th>
                                <th rowspan="2" class="align-middle">Total</th>
                                <th rowspan="2" class="align-middle">Rata-rata</th>
                            </tr>
                            <tr style="font-size: 0.75rem;">
                                @foreach($indicators as $indicator)
                                <th title="{{ $indicator->name }}">{{ explode(' (', $indicator->name)[0] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                            <tr>
                                <td class="text-start ps-4">
                                    <span class="fw-semibold">{{ $assignment->reviewer->name }}</span>
                                </td>
                                @php $rowSum = 0; @endphp
                                @foreach($indicators as $indicator)
                                    @php 
                                        $score = $assignment->assessments->where('indicator_id', $indicator->id)->first()->score ?? 0;
                                        $rowSum += $score;
                                    @endphp
                                    <td class="{{ $score >= 4 ? 'text-success' : ($score <= 2 ? 'text-danger' : '') }} fw-medium">
                                        {{ $score }}
                                    </td>
                                @endforeach
                                <td class="bg-light fw-bold text-primary">{{ $rowSum }}</td>
                                <td class="bg-light text-muted">{{ number_format($rowSum / count($indicators), 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start ps-4">Total per Variabel</td>
                                @foreach($indicators as $indicator)
                                <td class="text-primary">{{ $indicatorTotals[$indicator->id] }}</td>
                                @endforeach
                                <td colspan="2" rowspan="2" class="bg-primary text-white align-middle text-center">
                                    <div class="small opacity-75">NILAI AKHIR</div>
                                    <div class="fs-5">{{ number_format($finalAverage, 2) }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-start ps-4">Rata-rata per Indikator</td>
                                @foreach($indicators as $indicator)
                                <td class="text-primary">{{ number_format($indicatorAverages[$indicator->id], 2) }}</td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="col-lg-4 mb-4">
        <div class="card premium-card border-0 shadow-sm h-100 bg-primary text-white overflow-hidden position-relative">
            <div class="card-body position-relative" style="z-index: 1;">
                <h6 class="text-white text-opacity-75 mb-3">NILAI AKHIR KARYAWAN</h6>
                <div class="d-flex align-items-baseline">
                    <h1 class="display-4 fw-bold mb-0">{{ number_format($finalAverage, 2) }}</h1>
                    <span class="ms-2 fs-5">/ 40</span>
                </div>
                <div class="mt-4">
                    <p class="mb-1 opacity-75">Berdasarkan {{ $assignments->count() }} Penilai</p>
                    <div class="progress" style="height: 8px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" role="progressbar" style="width: {{ ($finalAverage / 40) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <i class="fas fa-chart-line position-absolute" style="font-size: 8rem; right: -20px; bottom: -20px; opacity: 0.1;"></i>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card premium-card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center p-4">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="display-1 fw-bold text-primary">{{ $gradeInfo['grade'] }}</div>
                        <span class="badge bg-primary px-3 py-1 rounded-pill">GRADE</span>
                    </div>
                    <div class="col-md-9">
                        <h5 class="fw-bold text-dark mb-3">Kesimpulan Hasil Penilaian</h5>
                        <p class="text-muted border-start border-4 border-primary ps-3 py-1 mb-0" style="font-style: italic; font-size: 1.1rem;">
                            "{{ $gradeInfo['description'] }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
