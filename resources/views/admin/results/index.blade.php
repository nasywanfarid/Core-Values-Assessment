@extends('layouts.admin')

@section('title', 'Hasil Penilaian - Periode')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-0 fw-bold text-primary">Daftar Periode Penilaian</h5>
                    <p class="text-muted small mb-0">Kelola dan lihat hasil penilaian karyawan per periode.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('admin.results.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        <select name="branch_id" class="form-select form-select-sm border-0 rounded px-3 text-muted fw-normal" onchange="this.form.submit()" style="min-width: 180px; background-color: #f3f6f9; font-size: 0.85rem; height: 38px; cursor: pointer;">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @if(request('branch_id'))
                            <a href="{{ route('admin.results.index') }}" class="btn btn-sm rounded-circle border-0 text-muted" title="Bersihkan Filter" style="background-color: #f3f6f9; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success mx-4 mt-3">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Periode Tanggal</th>
                                <th>Cabang</th>
                                <th class="text-center">Jumlah Karyawan Dinilai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periods as $period)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($period->assessment_date)->isoFormat('D MMMM YYYY') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info" style="font-size: 0.75rem;">
                                        {{ $period->branch_name }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.75rem;">
                                        {{ $period->employee_count }} Karyawan
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.results.period', ['date' => $period->assessment_date, 'branch' => $period->branch_id]) }}" class="btn btn-primary btn-sm rounded-pill d-inline-flex align-items-center justify-content-center" style="width: 95px; height: 32px;">
                                            <i class="fas fa-eye me-1"></i> Lihat
                                        </a>
                                        
                                        @if(auth()->user()->role === 'admin')
                                        <form id="delete-period-{{ $loop->index }}" action="{{ route('admin.results.destroy') }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="date" value="{{ $period->assessment_date }}">
                                            <input type="hidden" name="branch_id" value="{{ $period->branch_id }}">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill d-inline-flex align-items-center justify-content-center" style="width: 95px; height: 32px;" onclick="confirmDelete('delete-period-{{ $loop->index }}', 'Seluruh data penilaian pada periode dan cabang ini akan dihapus permanen!')">
                                                <i class="fas fa-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Belum ada data periode penilaian yang tersedia.</p>
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
