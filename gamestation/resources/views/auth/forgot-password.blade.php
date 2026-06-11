<x-guest-layout>
    <section class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="gs-auth-panel">
            <span class="gs-auth-kicker">Quên mật khẩu</span>
            <h1 class="gs-auth-title">Đặt lại mật khẩu</h1>
            <p class="gs-auth-subtitle">Nhập email và mật khẩu mới để đặt lại mật khẩu và đăng nhập.</p>

            <x-auth-session-status class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700" :status="session('status')" />

            <form method="POST" action="{{ route('password.direct') }}" class="mt-8 space-y-4">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="mt-2 w-full gs-auth-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Nhập email của bạn" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Mật khẩu mới')" />
                    <x-text-input id="password" class="mt-2 w-full gs-auth-input" type="password" name="password" required autocomplete="new-password" placeholder="Mật khẩu mới" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />
                    <x-text-input id="password_confirmation" class="mt-2 w-full gs-auth-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Xác nhận mật khẩu" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-4">
                    <a href="{{ route('login') }}" class="gs-auth-link">Quay lại đăng nhập</a>
                    <x-primary-button class="gs-auth-button">
                        {{ __('Đặt lại mật khẩu và đăng nhập') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </section>
</x-guest-layout>
