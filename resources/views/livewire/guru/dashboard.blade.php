<div>
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary shadow-sm">
                    <i class="bi bi-person-fill"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Siswa Terdaftar</span>
                    <span class="info-box-number">{{ $studentCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-success shadow-sm">
                    <i class="bi bi-building"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Sekolah Diampu</span>
                    <span class="info-box-number">{{ $schoolCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning shadow-sm">
                    <i class="bi bi-pencil-square"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Essay Menunggu Nilai</span>
                    <span class="info-box-number">{{ $pendingEssays }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger shadow-sm">
                    <i class="bi bi-journal-text"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Materi</span>
                    <span class="info-box-number">{{ $courseCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 col-xl-8">
            <div class="card guru-card">
                <div class="card-header border-0 pb-0">
                    <p class="guru-kicker mb-1">Dashboard</p>
                    <h3 class="card-title mb-0">Ringkasan aktivitas guru</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Gunakan halaman ini untuk melihat ringkasan kelas yang diampu, jumlah essay yang menunggu penilaian, dan akses cepat ke menu utama pengelolaan pembelajaran.</p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('guru.courses.index') }}" class="btn btn-primary">
                            Kelola Materi
                        </a>
                        <a href="{{ route('guru.essay-reviews.index') }}" class="btn btn-outline-warning">
                            Buka Penilaian Essay
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card guru-card h-100">
                <div class="card-header border-0 pb-0">
                    <p class="guru-kicker mb-1">Status Cepat</p>
                    <h3 class="card-title mb-0">Penilaian essay</h3>
                </div>
                <div class="card-body">
                    @if($pendingEssays > 0)
                        <div class="rounded-4 border border-warning-subtle bg-warning-subtle p-4 text-warning-emphasis">
                            Ada <strong>{{ $pendingEssays }}</strong> jawaban essay siswa yang menunggu penilaian.
                        </div>
                    @else
                        <div class="rounded-4 border border-success-subtle bg-success-subtle p-4 text-success-emphasis">
                            Saat ini tidak ada essay yang menunggu penilaian.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
