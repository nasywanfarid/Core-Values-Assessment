@extends('layouts.admin')

@section('title', 'Matriks Interaksi')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <!-- Form Tambah Hubungan -->
            <div class="col-lg-4 mb-4">
                <div class="card premium-card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Tambah Hubungan Global</h6>
                        <form action="{{ route('admin.interaction-matrices.store-interaction') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">DIVISI YANG DINILAI</label>
                                <select name="target_division_id" class="form-select bg-light border-0 py-2" required>
                                    <option value="">Pilih Divisi...</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">DIVISI PENILAI</label>
                                <select name="reviewer_division_id" class="form-select bg-light border-0 py-2" required>
                                    <option value="">Pilih Divisi...</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded fw-bold shadow-sm">
                                <i class="fas fa-link me-1"></i> HUBUNGKAN DIVISI
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List Hubungan -->
            <div class="col-lg-8 mb-4">
                <div class="card premium-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold mb-0">Daftar Hubungan Aktif</h6>
                            @if(auth()->user()->role === 'admin' && $interactions->count() > 0)
                            <form id="bulk-delete-interactions" action="{{ route('admin.interaction-matrices.bulk-destroy') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="confirmDelete('bulk-delete-interactions', 'Apakah Anda yakin ingin menghapus SEMUA hubungan divisi global ini?')">
                                    <i class="fas fa-trash me-1"></i> Hapus Semua
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-muted small fw-bold">
                                        <th class="ps-4 py-3 border-0">DIVISI YANG DINILAI</th>
                                        <th class="py-3 border-0">DIVISI PENILAI</th>
                                        <th class="text-end pe-4 py-3 border-0">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($interactions as $inter)
                                    <tr>
                                        <td class="ps-4 py-3 border-0 fw-bold">{{ $inter->targetDivision->name }}</td>
                                        <td class="py-3 border-0">
                                            <span class="badge bg-light text-dark border px-3 py-2 rounded-3 fw-normal" style="font-size: 0.9rem;">
                                                {{ $inter->reviewerDivision->name }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 py-3 border-0">
                                            @if(auth()->user()->role === 'admin')
                                            <form id="delete-interaction-{{ $inter->id }}" action="{{ route('admin.interaction-matrices.destroy-interaction', $inter->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('delete-interaction-{{ $inter->id }}', 'Hapus hubungan divisi ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">Belum ada hubungan divisi yang diatur.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
