@extends('layouts.admin')

@section('title', 'Tambah Indikator')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card premium-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('admin.indicators.index') }}" class="btn btn-sm btn-light rounded-pill me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h5 class="fw-bold mb-0">Tambah Indikator Baru</h5>
                </div>

                <form action="{{ route('admin.indicators.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Indikator</label>
                        <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Kejujuran (Honesty)" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Deskripsi Umum</label>
                        <textarea class="form-control rounded-3" id="description" name="description" rows="3" placeholder="Masukkan deskripsi singkat tentang indikator ini..." required>{{ old('description') }}</textarea>
                    </div>

                    <h6 class="fw-bold mb-3">Deskripsi Skala Penilaian</h6>
                    
                    @for ($i = 1; $i <= 5; $i++)
                    <div class="mb-3">
                        <label for="scale_{{ $i }}" class="form-label fw-semibold">
                            Skala {{ $i }} 
                            @if($i == 1) <span class="badge bg-danger ms-2">Sangat Kurang</span> @endif
                            @if($i == 2) <span class="badge bg-warning text-dark ms-2">Kurang</span> @endif
                            @if($i == 3) <span class="badge bg-info text-dark ms-2">Cukup</span> @endif
                            @if($i == 4) <span class="badge bg-primary ms-2">Baik</span> @endif
                            @if($i == 5) <span class="badge bg-success ms-2">Sangat Baik</span> @endif
                        </label>
                        <textarea class="form-control rounded-3" id="scale_{{ $i }}" name="scale_{{ $i }}" rows="2" placeholder="Deskripsi untuk skor {{ $i }}..." required>{{ old('scale_'.$i) }}</textarea>
                    </div>
                    @endfor

                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-primary py-2 rounded-pill fw-bold">
                            <i class="fas fa-plus me-2"></i> Simpan Indikator
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
