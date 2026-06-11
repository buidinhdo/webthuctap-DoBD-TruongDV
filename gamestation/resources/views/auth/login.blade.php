<x-guest-layout>
    <section class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="gs-auth-panel">
            <span class="gs-auth-kicker">Đăng nhập</span>
            <h1 class="gs-auth-title">Đăng nhập tài khoản</h1>
            <p class="gs-auth-subtitle">Đăng nhập bằng email và mật khẩu để tiếp tục mua sắm.</p>

            <x-auth-session-status class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-4">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="mt-2 w-full gs-auth-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Nhập email của bạn" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Mật khẩu')" />
                    <x-text-input id="password" class="mt-2 w-full gs-auth-input" type="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                    <label for="remember_me" class="inline-flex items-center gap-2 text-slate-600">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" name="remember">
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a class="gs-auth-link" href="{{ route('password.request') }}">Quên mật khẩu?</a>
                    @endif
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-4">
                    <a href="{{ route('register') }}" class="gs-auth-link">Chưa có tài khoản? Đăng ký</a>
                    <x-primary-button class="gs-auth-button">
                        {{ __('Đăng nhập') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </section>
</x-guest-layout>
