@extends('layouts.admin')

@section('title', 'User Profile')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card premium-card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-primary">Informasi Profil</h5>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4361ee&color=fff&size=128" 
                             alt="Avatar" class="rounded-circle shadow-sm mb-3" width="100">
                        <h4 class="fw-bold mb-1">{{ auth()->user()->name }}</h4>
                        <span class="badge bg-primary rounded-pill px-3">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                             <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Alamat Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(auth()->user()->role === 'admin')
                            {{-- Editable dropdowns for admin --}}
                            <div class="col-md-4">
                                <label for="division_id" class="form-label fw-semibold">Divisi</label>
                                <select class="form-select @error('division_id') is-invalid @enderror"
                                        id="division_id" name="division_id" required>
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ old('division_id', auth()->user()->division_id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="branch_id" class="form-label fw-semibold">Cabang</label>
                                <select class="form-select @error('branch_id') is-invalid @enderror"
                                        id="branch_id" name="branch_id" required>
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id', auth()->user()->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="position_id" class="form-label fw-semibold">Jabatan</label>
                                <select class="form-select @error('position_id') is-invalid @enderror"
                                        id="position_id" name="position_id">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}"
                                            {{ old('position_id', auth()->user()->position_id) == $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @else
                            {{-- Readonly for non-admin users --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Divisi</label>
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->division->name ?? '-' }}" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Cabang</label>
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->branch->name ?? '-' }}" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jabatan</label>
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->position->name ?? '-' }}" readonly>
                            </div>
                            @endif

                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Ubah Password (Opsional)</h6>

                            <div class="col-md-12">
                                <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control pe-5 @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password">
                                    <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y text-muted" type="button" onclick="togglePasswordVisibility('current_password')" style="background: none; box-shadow: none; z-index: 5;">
                                        <i class="fas fa-eye" id="icon-current_password"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="new_password" class="form-label fw-semibold">Password Baru</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control pe-5 @error('new_password') is-invalid @enderror" 
                                           id="new_password" name="new_password">
                                    <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y text-muted" type="button" onclick="togglePasswordVisibility('new_password')" style="background: none; box-shadow: none; z-index: 5;">
                                        <i class="fas fa-eye" id="icon-new_password"></i>
                                    </button>
                                </div>
                                @error('new_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control pe-5" 
                                           id="new_password_confirmation" name="new_password_confirmation">
                                    <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y text-muted" type="button" onclick="togglePasswordVisibility('new_password_confirmation')" style="background: none; box-shadow: none; z-index: 5;">
                                        <i class="fas fa-eye" id="icon-new_password_confirmation"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
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
</script>
@endpush
