@extends('layouts.admin')

@section('title', 'Data Divisi')

@section('content')
<div class="card premium-card border-0 mb-4">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1">Manajemen Divisi</h5>
            <p class="text-muted mb-0">Kelola daftar divisi perusahaan Anda di sini.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-2"></i> Tambah Divisi
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card premium-card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Divisi</th>
                        <th>Tanggal Dibuat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($divisions as $index => $division)
                    <tr>
                        <td class="ps-4">{{ $divisions->firstItem() + $index }}</td>
                        <td class="fw-medium text-dark">{{ $division->name }}</td>
                        <td class="text-muted">{{ $division->created_at->format('d M Y') }}</td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $division->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(auth()->user()->role !== 'hr')
                            <form id="delete-divisi-{{ $division->id }}" action="{{ route('admin.divisions.destroy', $division->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('delete-divisi-{{ $division->id }}', 'Divisi {{ $division->name }} akan dihapus!')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fs-2 mb-3 d-block text-black-50"></i>
                            Belum ada data divisi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($divisions->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $divisions->links() }}
    </div>
    @endif
</div>

@foreach($divisions as $division)
<!-- Edit Modal -->
<div class="modal fade" id="editModal{{ $division->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.divisions.update', $division->id) }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Divisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-3">
                    <label class="form-label text-muted fw-semibold">Nama Divisi</label>
                    <input type="text" name="name" class="form-control form-control-lg bg-light border-0" value="{{ $division->name }}" required>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.divisions.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Divisi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-3">
                    <label class="form-label text-muted fw-semibold">Nama Divisi</label>
                    <input type="text" name="name" class="form-control form-control-lg bg-light border-0" placeholder="Masukkan nama divisi..." required>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Divisi</button>
            </div>
        </form>
    </div>
</div>
@endsection
