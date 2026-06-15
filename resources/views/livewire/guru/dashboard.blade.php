<div>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
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
        <div class="col-12 col-sm-6 col-md-3">
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
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning shadow-sm">
                    <i class="bi bi-pencil-square"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending Essay</span>
                    <span class="info-box-number">{{ $pendingEssays }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Selamat Datang</h3>
                </div>
                <div class="card-body">
                    <p>Selamat datang di panel guru {{ config('app.name') }}. Gunakan menu di samping untuk mengelola materi, kuis, dan melihat laporan hasil belajar siswa.</p>
                </div>
            </div>
        </div>
    </div>
</div>
