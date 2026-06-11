<div class="login-box">
    <div class="login-logo">
        <a href="/"><b>LMS</b> SIG</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">{{ $subtitle }}</p>

            <form wire:submit.prevent="login">
                <div class="input-group mb-3">
                    <input type="text" wire:model="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username">
                    <div class="input-group-text">
                        <span class="bi bi-person-fill"></span>
                    </div>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="input-group mb-3">
                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                    <div class="input-group-text">
                        <span class="bi bi-lock-fill"></span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--begin::Row-->
                <div class="row">
                    <div class="col-8">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Masuk</button>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!--end::Row-->
            </form>

            <p class="mb-1 mt-3">
                <a href="#">Lupa password?</a>
            </p>

            <p class="mt-3 mb-0 text-center">
                <a href="{{ $switch_url }}">{{ $switch_label }}</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
