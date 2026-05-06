@extends('layouts.admin')

@section('title', 'Form Penilaian Core Values')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card premium-card border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="text-white-50 text-uppercase fw-bold mb-1">Mengevaluasi Karyawan</p>
                        <h2 class="fw-bold mb-2">{{ $assignment->reviewee->name }}</h2>
                        <ul class="list-inline mb-0 text-white-50">
                            <li class="list-inline-item"><i class="fas fa-id-badge me-1"></i> NIP: {{ $assignment->reviewee->nip ?? '-' }}</li>
                            <li class="list-inline-item"><i class="fas fa-sitemap me-1 ms-3"></i> Divisi: {{ $assignment->reviewee->division->name ?? '-' }}</li>
                        </ul>
                    </div>
                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                        <img src="https://ui-avatars.com/api/?name={{ $assignment->reviewee->name }}&background=fff&color=4361ee&size=100" class="rounded-circle shadow-lg border border-3 border-white">
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('karyawan.evaluate.store', $assignment->id) }}" method="POST">
            @csrf
            
            <div class="alert alert-info border-0 bg-info bg-opacity-10 text-primary shadow-sm rounded-3 mb-4 p-4">
                <h5 class="fw-bold"><i class="fas fa-info-circle me-2"></i> Panduan Penilaian</h5>
                <p class="mb-0">Berikan penilaian yang objektif pada 8 indikator <strong>BERHASIL</strong> di bawah ini (skala 1-5). Anda juga dapat memberikan alasan singkat untuk mendukung penilaian yang Anda berikan.</p>
            </div>

            @foreach($indicators as $index => $indicator)
            <div class="card premium-card border-0 mb-4 indicator-card">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex mb-4 align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center fw-bold fs-4 shadow-sm" style="width: 50px; height: 50px;">
                            {{ $index + 1 }}
                        </div>
                        <div class="ms-3">
                            <h4 class="fw-bold text-dark mb-0">{{ $indicator->name }}</h4>
                            <p class="text-muted mb-0">{{ $indicator->description }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-4 mb-md-0">
                            <label class="form-label fw-bold text-dark mb-3">Skala Penilaian (1-5)</label>
                            
                            <div class="scale-descriptions mb-4 p-3 bg-light rounded-3 small">
                                <div class="mb-2"><span class="badge bg-danger me-2">1</span> {{ $indicator->scale_1 }}</div>
                                <div class="mb-2"><span class="badge bg-warning text-dark me-2">2</span> {{ $indicator->scale_2 }}</div>
                                <div class="mb-2"><span class="badge bg-info text-dark me-2">3</span> {{ $indicator->scale_3 }}</div>
                                <div class="mb-2"><span class="badge bg-primary me-2">4</span> {{ $indicator->scale_4 }}</div>
                                <div class="mb-0"><span class="badge bg-success me-2">5</span> {{ $indicator->scale_5 }}</div>
                            </div>

                            <div class="d-flex justify-content-between scale-options px-2">
                                @for($i = 1; $i <= 5; $i++)
                                <div class="form-check p-0 me-2 position-relative">
                                    <input type="radio" class="btn-check" name="scores[{{ $indicator->id }}]" id="score_{{ $indicator->id }}_{{ $i }}" value="{{ $i }}" required>
                                    <label class="btn btn-outline-primary fw-bold rounded-circle d-flex justify-content-center align-items-center transition-all shadow-sm" style="width: 50px; height: 50px;" for="score_{{ $indicator->id }}_{{ $i }}">{{ $i }}</label>
                                </div>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark mb-2">Alasan / Keterangan (Wajib)</label>
                            <textarea class="form-control bg-light border-0 shadow-sm p-3" name="reasons[{{ $indicator->id }}]" rows="6" placeholder="Tulis alasan spesifik Anda mengapa memberikan nilai tersebut..." required></textarea>
                            <div class="form-text mt-2"><i class="fas fa-exclamation-triangle me-1"></i> Mohon berikan alasan yang objektif.</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="card premium-card border-0 bg-transparent shadow-none mb-5">
                <div class="card-body p-0 d-flex justify-content-between">
                    <a href="{{ route('karyawan.dashboard') }}" class="btn btn-outline-secondary px-4 fw-bold rounded-pill">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                        <i class="fas fa-paper-plane me-1"></i> Submit Penilaian
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .btn-check:checked + .btn-outline-primary {
        background-color: #4361ee;
        color: white;
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.4);
    }
    .btn-outline-primary:hover {
        background-color: rgba(67, 97, 238, 0.1);
        color: #4361ee;
    }
</style>
<script>
    // Aesthetic interaction for focus states
    document.querySelectorAll('textarea').forEach(textarea => {
        textarea.addEventListener('focus', function() {
            this.closest('.indicator-card').style.transform = 'translateY(-5px)';
            this.closest('.indicator-card').style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
        });
        textarea.addEventListener('blur', function() {
            this.closest('.indicator-card').style.transform = 'none';
            this.closest('.indicator-card').style.boxShadow = '0 4px 15px rgba(0,0,0,0.04)';
        });
    });
</script>
@endpush
