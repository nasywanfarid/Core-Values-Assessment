@extends('layouts.admin')

@section('title', 'Data Karyawan')

@section('content')
<style>
    @media (max-width: 767.98px) {
        .mobile-header {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .mobile-stack {
            flex-direction: column !important;
            align-items: stretch !important;
            width: 100% !important;
        }
        .mobile-filter {
            width: 100% !important;
        }
    }
</style>
<div class="card premium-card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between flex-wrap gap-3 mobile-header">
        <h5 class="mb-0 fw-bold text-primary">Daftar Karyawan</h5>
        <div class="d-flex align-items-center flex-wrap flex-md-nowrap gap-2 mobile-stack">
            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex align-items-center flex-wrap flex-md-nowrap gap-2 mobile-stack">
                <!-- Branch Filter -->
                <select name="branch_id" class="form-select form-select-sm border-0 rounded px-3 text-muted fw-normal mobile-filter" onchange="this.form.submit()" style="width: 170px; background-color: #f3f6f9; font-size: 0.85rem; height: 38px; cursor: pointer;">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>

                <!-- Division Filter -->
                <select name="division_id" class="form-select form-select-sm border-0 rounded px-3 text-muted fw-normal mobile-filter" onchange="this.form.submit()" style="width: 170px; background-color: #f3f6f9; font-size: 0.85rem; height: 38px; cursor: pointer;">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                    @endforeach
                </select>

                <!-- Search Input -->
                <div class="input-group mobile-filter" style="width: 170px;">
                    <input type="text" name="search" class="form-control form-control-sm border-0 rounded-start px-3 text-muted fw-normal" placeholder="Cari karyawan..." value="{{ request('search') }}" style="background-color: #f3f6f9; font-size: 0.85rem; height: 38px;">
                    <button class="btn btn-sm border-0 rounded-end text-muted" type="submit" style="background-color: #f3f6f9; height: 38px;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                @if(request('branch_id') || request('division_id') || request('search'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm rounded-circle border-0 text-muted mobile-filter" title="Bersihkan Filter" style="background-color: #f3f6f9; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
            <button type="button" class="btn btn-primary rounded px-4 mobile-filter" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i> Karyawan
            </button>
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
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                Nama
                                <span class="ms-1">
                                    @if(request('sort') == 'name')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                    @else
                                        <i class="fas fa-sort text-muted opacity-25"></i>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => request('sort') == 'email' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                Email
                                <span class="ms-1">
                                    @if(request('sort') == 'email')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                    @else
                                        <i class="fas fa-sort text-muted opacity-25"></i>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nip', 'direction' => request('sort') == 'nip' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                NIP
                                <span class="ms-1">
                                    @if(request('sort') == 'nip')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                    @else
                                        <i class="fas fa-sort text-muted opacity-25"></i>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th>Cabang</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'division', 'direction' => request('sort') == 'division' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                Divisi
                                <span class="ms-1">
                                    @if(request('sort') == 'division')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                    @else
                                        <i class="fas fa-sort text-muted opacity-25"></i>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4361ee&color=fff" 
                                     class="rounded-circle me-2" width="35" height="35">
                                <span class="fw-medium">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->nip ?? '-' }}</td>
                        <td><span class="badge bg-info bg-opacity-10 text-info" style="font-size: 0.75rem;">{{ $user->branch->name ?? '-' }}</span></td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.75rem;">{{ $user->division->name ?? '-' }}</span>
                        </td>

                        <td class="text-center text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(auth()->user()->role !== 'hr')
                            <form id="delete-user-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('delete-user-{{ $user->id }}', 'Karyawan {{ $user->name }} akan dihapus secara permanen!')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data karyawan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $users->links() }}
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addUserModalLabel">Tambah Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- 1. Nama Lengkap -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <!-- 2. NIP -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP</label>
                        <input type="text" name="nip" class="form-control">
                    </div>
                    <!-- 3. Cabang & Divisi -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Cabang</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Divisi</label>
                            <select name="division_id" class="form-select" required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- 4. Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback fw-medium">
                                Email ini sudah terdaftar. Silakan gunakan email yang lain.
                            </div>
                        @enderror
                    </div>
                    <!-- 5. Password -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="addPassword" class="form-control" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('addPassword')">
                                <i class="fas fa-eye" id="icon-addPassword"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($users as $user)
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editUserModalLabel{{ $user->id }}">Edit Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- 1. Nama Lengkap -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <!-- 2. NIP -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ $user->nip }}">
                    </div>
                    <!-- 3. Cabang & Divisi -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Cabang</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Divisi</label>
                            <select name="division_id" class="form-select" required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ $user->division_id == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- 4. Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <!-- 5. Password -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Password (Kosongkan jika tidak ingin diubah)</label>
                        <div class="input-group">
                            <input type="password" name="password" id="editPassword{{ $user->id }}" class="form-control" minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('editPassword{{ $user->id }}')">
                                <i class="fas fa-eye" id="icon-editPassword{{ $user->id }}"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById('icon-' + inputId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    @if($errors->any() && !old('_method'))
    document.addEventListener('DOMContentLoaded', function() {
        var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        addUserModal.show();
    });
    @endif
</script>
@endpush
