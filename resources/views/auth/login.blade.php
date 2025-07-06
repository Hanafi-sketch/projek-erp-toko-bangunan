<x-guest-layout>
    <div class="login-box">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                <input id="email" class="input-field mt-1" type="email" name="email" required autofocus />
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                <input id="password" class="input-field mt-1" type="password" name="password" required autocomplete="current-password" />
                @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full">Masuk</button>
        </form>

        <!-- Daftar akun -->
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Belum punya akun?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">Daftar Akun</a>
            </p>
        </div>
    </div>
</x-guest-layout>
