@extends('layouts.admin')

@section('title', 'Daftar Penilaian - ' . $branch->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.results.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h5 class="mb-0 fw-bold">Periode: {{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM YYYY') }}</h5>
                            <p class="text-muted small mb-0">Cabang: {{ $branch->name }}</p>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('admin.results.export-excel', ['date' => $date, 'branch' => $branch->id]) }}" class="btn btn-success btn-sm px-3 rounded-pill">
                            <i class="fas fa-file-excel me-1"></i> Download Excel
                        </a>

                        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 align-items-center">
                            <select name="division_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Semua Divisi</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama karyawan..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                            @if(request('division_id') || request('search'))
                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light rounded-circle" title="Reset Filter">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Karyawan</th>
                                <th>Divisi</th>
                                <th class="text-center">Total Nilai</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $item)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($item->name) }}&background=ec4899&color=fff" 
                                             class="rounded-circle me-3" width="38" height="38">
                                        <span class="fw-medium">{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.75rem;">{{ $item->division }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-primary fs-5">{{ $item->total_score }}</div>
                                    <small class="text-muted">/ 40</small>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = match($item->grade) {
                                            'A' => 'bg-success',
                                            'B' => 'bg-primary',
                                            'C' => 'bg-info',
                                            'D' => 'bg-warning text-dark',
                                            'E' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-3 py-2 fs-6 rounded-pill shadow-sm" style="min-width: 45px;">
                                        {{ $item->grade }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.results.detail', ['date' => $date, 'branch' => $branch->id, 'user' => $item->id]) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                        Detail <i class="fas fa-eye ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Tidak ada data karyawan yang sesuai filter.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
