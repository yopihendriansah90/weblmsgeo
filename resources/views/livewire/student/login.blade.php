<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Login Siswa</h1>
            <p class="mt-1 text-sm text-neutral-600">Masuk dengan username dan password siswa.</p>
        </div>

        <form wire:submit="login" class="space-y-4">
            <div>
                <label class="text-sm font-medium" for="username">Username</label>
                <input id="username" wire:model="username" class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 focus:border-emerald-600 focus:outline-none" autocomplete="username">
                @error('username') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium" for="password">Password</label>
                <input id="password" type="password" wire:model="password" class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 focus:border-emerald-600 focus:outline-none" autocomplete="current-password">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <button class="w-full rounded-md bg-emerald-700 px-4 py-2 font-medium text-white hover:bg-emerald-800" type="submit">Masuk</button>
        </form>
    </div>
</div>
