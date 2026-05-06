@extends('layouts.admin')

@section('title', 'Matriks Penilaian')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Filter Cabang & Tanggal</h5>
                <form action="{{ route('admin.interaction-matrices.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold">PILIH CABANG</label>
                        <select name="branch_id" class="form-select bg-light border-0 py-2" required onchange="this.form.submit()">
                            <option value="">Pilih Cabang...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold">TANGGAL PENILAIAN</label>
                        <input type="date" name="date" class="form-control bg-light border-0 py-2" value="{{ $selectedDate }}" required onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">
                            <i class="fas fa-search me-1"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($selectedBranchId && $selectedDate)
    <div class="col-12">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Matriks Penilai Antar Karyawan</h5>
                        <p class="text-muted mb-0 small">Cabang: <strong>{{ $branches->find($selectedBranchId)->name }}</strong> | Tanggal: <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong></p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="selectAllIntersections()">
                            <i class="fas fa-check-double me-1"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="clearAllIntersections()">
                            <i class="fas fa-times me-1"></i> Kosongkan
                        </button>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form action="{{ route('admin.store-assignments') }}" method="POST">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                    <input type="hidden" name="assessment_date" value="{{ $selectedDate }}">

                    <div class="table-responsive shadow-sm rounded-3" style="max-height: 65vh; overflow: auto; border: 1px solid #dee2e6; background: white;">
                        <table class="table table-bordered align-middle matrix-table mb-0" style="width: max-content; min-width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center bg-light" style="min-width: 200px; position: sticky; left: 0; top: 0; z-index: 30; border-right: 2px solid #dee2e6; border-bottom: 2px solid #dee2e6;">
                                        <div class="py-2">Penilai \ Dinilai</div>
                                    </th>
                                    @foreach($employees as $emp)
                                    <th class="text-center small bg-light" style="min-width: 160px; position: sticky; top: 0; z-index: 20; border-bottom: 2px solid #dee2e6;">
                                        <div class="px-2 py-3">
                                            <div class="fw-bold text-wrap">{{ $emp->name }}</div>
                                            <div class="text-muted x-small mt-1" style="font-size: 0.7rem;">{{ $emp->division->name ?? '-' }}</div>
                                        </div>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $reviewer)
                                <tr>
                                    <td class="bg-white fw-bold" style="position: sticky; left: 0; z-index: 10; border-right: 2px solid #dee2e6; min-width: 200px;">
                                        <div class="px-3 py-2">
                                            <div class="text-truncate">{{ $reviewer->name }}</div>
                                            <div class="text-muted x-small fw-normal text-truncate">{{ $reviewer->division->name ?? '-' }}</div>
                                        </div>
                                    </td>
                                    @foreach($employees as $reviewee)
                                    <td class="text-center p-0">
                                        @if($reviewer->id == $reviewee->id)
                                            <div class="w-100 h-100 bg-light py-3" title="Self assessment tidak diperbolehkan">
                                                <i class="fas fa-times text-muted opacity-25"></i>
                                            </div>
                                        @else
                                            @php
                                                $isChecked = isset($existingAssignments[$reviewer->id]) && in_array($reviewee->id, $existingAssignments[$reviewer->id]);
                                            @endphp
                                            <label class="d-block py-3 w-100 cursor-pointer matrix-label {{ $isChecked ? 'bg-primary bg-opacity-10' : '' }}" style="cursor: pointer;">
                                                <input type="checkbox" name="matrix[{{ $reviewer->id }}][]" value="{{ $reviewee->id }}" class="matrix-checkbox" {{ $isChecked ? 'checked' : '' }} onchange="handleCheckboxChange(this)">
                                            </label>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded fw-bold shadow-sm">
                            <i class="fas fa-magic me-2"></i> Simpan & Generate Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
    <div class="col-12 text-center py-5">
        <div class="p-5 bg-white rounded-4 shadow-sm">
            <i class="fas fa-sitemap fa-4x text-primary opacity-25 mb-4"></i>
            <h4 class="fw-bold">Silakan Pilih Cabang dan Tanggal</h4>
            <p class="text-muted">Pilih parameter di atas untuk menampilkan matriks penilai antar karyawan.</p>
        </div>
    </div>
    @endif

    <div class="col-12 mt-5">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-history me-2 text-muted"></i> Riwayat Penugasan (Sudah Digenerate)</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Cabang</th>
                                <th>Tanggal Penilaian</th>
                                <th>Total Penugasan</th>
                                <th>Progress</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $index => $item)
                            <tr>
                                <td>{{ $history->firstItem() + $index }}</td>
                                <td><span class="fw-bold">{{ $item->branch_name }}</span></td>
                                <td><span class="badge bg-light text-dark fw-normal fs-6">{{ \Carbon\Carbon::parse($item->assessment_date)->translatedFormat('d F Y') }}</span></td>
                                <td class="text-center">
                                    <div class="h5 mb-0 fw-bold">{{ $item->total_assignments }}</div>
                                    <div class="small text-muted">Penugasan</div>
                                </td>
                                <td style="width: 250px;">
                                    @php
                                        $percent = $item->total_assignments > 0 ? round(($item->completed_assignments / $item->total_assignments) * 100) : 0;
                                    @endphp
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span>{{ $item->completed_assignments }} Selesai</span>
                                        <span class="fw-bold">{{ $percent }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $percent == 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.interaction-matrices.index', ['branch_id' => $item->branch_id, 'date' => $item->assessment_date]) }}" class="btn btn-sm btn-info text-white rounded-pill px-3 me-2">
                                        <i class="fas fa-eye me-1"></i> Lihat Matriks
                                    </a>
                                    @if(auth()->user()->role !== 'hr')
                                    <form id="bulk-delete-{{ $index }}" action="{{ route('admin.bulk-destroy-assignments') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="branch_id" value="{{ $item->branch_id }}">
                                        <input type="hidden" name="date" value="{{ $item->assessment_date }}">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('bulk-delete-{{ $index }}', 'Hapus semua penugasan untuk cabang {{ $item->branch_name }} pada tanggal {{ \Carbon\Carbon::parse($item->assessment_date)->format('d/m/Y') }}?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada riwayat penugasan yang dibuat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $history->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .matrix-table th, .matrix-table td {
        padding: 0 !important;
        vertical-align: middle;
        border-color: #dee2e6 !important;
    }
    .matrix-table thead th {
        vertical-align: top;
    }
    .matrix-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .matrix-label {
        transition: all 0.2s ease;
    }
    .matrix-label:hover {
        background-color: rgba(236, 72, 153, 0.05) !important;
    }
    .x-small {
        font-size: 0.75rem;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    /* Shadow effects for sticky columns */
    .matrix-table td:first-child, .matrix-table th:first-child {
        box-shadow: 2px 0 5px rgba(0,0,0,0.03);
    }
</style>
<script>
    function handleCheckboxChange(checkbox) {
        if (checkbox.checked) {
            checkbox.closest('.matrix-label').classList.add('bg-primary', 'bg-opacity-10');
        } else {
            checkbox.closest('.matrix-label').classList.remove('bg-primary', 'bg-opacity-10');
        }
    }

    function selectAllIntersections() {
        document.querySelectorAll('.matrix-checkbox').forEach(cb => {
            cb.checked = true;
            handleCheckboxChange(cb);
        });
    }

    function clearAllIntersections() {
        document.querySelectorAll('.matrix-checkbox').forEach(cb => {
            cb.checked = false;
            handleCheckboxChange(cb);
        });
    }
</script>
@endpush
@endsection
