<div class="auth-shell">
    <div class="auth-card">
        <section class="auth-brand-panel" aria-label="{{ config('app.name') }}">
            <div class="auth-brand-content">
                <a href="/" class="auth-brand-mark" aria-label="{{ config('app.name') }}">
                    <span class="material-symbols-outlined auth-brand-icon">school</span>
                    <span>{{ config('app.name') }}</span>
                </a>

                <div class="auth-brand-copy">
                    <h1>Gerbang belajar SIG yang lebih terarah.</h1>
                    <p>Masuk ke ruang belajar digital untuk membaca materi, mengerjakan kuis, dan memantau progres pembelajaran.</p>
                </div>

                <div class="auth-stat-card">
                    <span class="auth-stat-icon material-symbols-outlined">trending_up</span>
                    <div>
                        <span>Portal aktif</span>
                        <strong>Guru dan siswa</strong>
                    </div>
                </div>
            </div>

            <img
                src="{{ asset('adminlte/assets/img/photo2.png') }}"
                alt="Ruang belajar modern"
                class="auth-brand-image"
            >
        </section>

        <section class="auth-form-panel">
            <div class="auth-mobile-brand">
                <span class="material-symbols-outlined auth-brand-icon">school</span>
                <span>{{ config('app.name') }}</span>
            </div>

            <div class="auth-heading">
                <h2>Selamat datang kembali</h2>
                <p>Masuk menggunakan username dan password akun guru atau siswa.</p>
            </div>

            <form wire:submit.prevent="login" class="auth-form">
                <div class="auth-field">
                    <label for="username">Username</label>
                    <div class="auth-input-wrap @error('username') is-invalid @enderror">
                        <span class="material-symbols-outlined">alternate_email</span>
                        <input
                            id="username"
                            type="text"
                            wire:model="username"
                            autocomplete="username"
                            placeholder="contoh: hasyyati atau adzkiya"
                            autofocus
                        >
                    </div>
                    @error('username')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-field">
                    <div class="auth-label-row">
                        <label for="password">Password</label>
                        <span class="auth-muted-link">Hubungi admin jika lupa</span>
                    </div>
                    <div class="auth-input-wrap @error('password') is-invalid @enderror">
                        <span class="material-symbols-outlined">lock</span>
                        <input
                            id="password"
                            type="password"
                            wire:model="password"
                            autocomplete="current-password"
                            placeholder="Masukkan password"
                        >
                        <button class="auth-eye" type="button" aria-label="Tampilkan password" onclick="const input = document.getElementById('password'); const icon = this.querySelector('.material-symbols-outlined'); input.type = input.type === 'password' ? 'text' : 'password'; icon.textContent = input.type === 'password' ? 'visibility' : 'visibility_off';">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <label class="auth-remember" for="remember">
                    <input id="remember" type="checkbox" wire:model="remember">
                    <span>Ingat perangkat ini</span>
                </label>

                <button type="submit" class="auth-submit" wire:loading.attr="disabled" wire:target="login">
                    <span wire:loading.remove wire:target="login">Masuk ke {{ config('app.name') }}</span>
                    <span wire:loading wire:target="login">Memproses...</span>
                    <span class="material-symbols-outlined">login</span>
                </button>
            </form>

            <div class="auth-footer-note">
                Belum punya akun? <span>Data akun dibuat oleh Super Admin.</span>
            </div>
        </section>
    </div>
</div>
