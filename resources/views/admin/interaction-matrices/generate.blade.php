@extends('layouts.admin')

@section('title', 'Generate Matriks Penilaian')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card premium-card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Generate Penugasan Baru</h6>
                <form action="{{ route('admin.generate-assessments.generate') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">PILIH CABANG</label>
                        <select name="branch_id" class="form-select bg-light border-0 py-2" required>
                            <option value="">Pilih Cabang...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">PERIODE PENILAIAN</label>
                        <style>
                            .month-input-wrapper {
                                position: relative;
                            }
                            input[type="month"] {
                                position: relative;
                                cursor: pointer;
                            }
                            input[type="month"]::before {
                                content: attr(placeholder);
                                color: #212529; /* Sesuai warna teks select */
                                background-color: #f8f9fa;
                                position: absolute;
                                left: 12px;
                                top: 50%;
                                transform: translateY(-50%);
                                padding-right: 20px;
                                width: 80%;
                                pointer-events: none;
                                font-weight: 400; /* Normal weight agar sama dengan select */
                            }
                            input[type="month"]:focus::before,
                            input[type="month"]:valid::before {
                                display: none;
                            }
                        </style>
                        <div class="month-input-wrapper">
                            <input type="month" name="date" class="form-control bg-light border-0 py-2" placeholder="Pilih Periode..." required onclick="this.showPicker()">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">JUMLAH PENILAI</label>
                        <select name="count" class="form-select bg-light border-0 py-2" required>
                            <option value="">Pilih Jumlah Penilai...</option>
                            <option value="3">3 Penilai</option>
                            <option value="4">4 Penilai</option>
                            <option value="5">5 Penilai</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded fw-bold shadow-sm">
                        <i class="fas fa-magic me-1"></i> GENERATE MATRIKS
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card premium-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Riwayat & Status Penilaian</h6>
                
                @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="bg-light">
                            <tr class="text-muted small fw-bold">
                                <th class="ps-3 py-3 border-0">CABANG / PERIODE</th>
                                <th class="py-3 border-0 text-center">JUMLAH PENILAI</th>
                                <th class="py-3 border-0 text-center">TOTAL / SELESAI</th>
                                <th class="py-3 border-0 text-center">STATUS AKSES</th>
                                <th class="text-end pe-3 py-3 border-0">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $index => $item)
                            <tr>
                                <td class="ps-3 py-3 border-0">
                                    <div class="fw-bold">{{ $item->branch_name }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($item->assessment_date)->translatedFormat('F Y') }}</small>
                                </td>
                                <td class="py-3 border-0 text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">{{ $item->reviewer_count }} Orang</span>
                                </td>
                                <td class="py-3 border-0 text-center">
                                    <span class="badge bg-primary rounded-pill px-3">{{ $item->total_assignments }}</span>
                                    <span class="badge bg-success rounded-pill px-3">{{ $item->completed_assignments }}</span>
                                </td>
                                <td class="py-3 border-0 text-center">
                                    <form action="{{ route('admin.generate-assessments.toggle', ['date' => $item->assessment_date, 'branch' => $item->branch_id]) }}" method="POST">
                                        @csrf
                                        @if($item->all_active)
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                                                <i class="fas fa-lock-open me-1"></i> Terbuka
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                                                <i class="fas fa-lock me-1"></i> Tertutup
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td class="text-end pe-3 py-3 border-0">
                                    @if(auth()->user()->role === 'admin')
                                    <form id="delete-history-{{ $index }}" action="{{ route('admin.generate-assessments.bulk-destroy') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="branch_id" value="{{ $item->branch_id }}">
                                        <input type="hidden" name="date" value="{{ $item->assessment_date }}">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('delete-history-{{ $index }}', 'Hapus semua penugasan periode ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small">Belum ada riwayat generate.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $history->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
