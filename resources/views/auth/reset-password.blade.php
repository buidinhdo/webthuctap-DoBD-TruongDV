<x-guest-layout>
    <section class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="gs-auth-panel">
            <span class="gs-auth-kicker">Đặt lại mật khẩu</span>
            <h1 class="gs-auth-title">Tạo mật khẩu mới</h1>
            <p class="gs-auth-subtitle">Nhập email và mật khẩu mới để hoàn tất việc đặt lại.</p>

            <form method="POST" action="{{ route('password.direct') }}" class="mt-8 space-y-4">
                @csrf


                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="mt-2 w-full gs-auth-input" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="Email của bạn" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Mật khẩu')" />
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
                    <x-primary-button class="gs-auth-button">{{ __('Đặt lại mật khẩu') }}</x-primary-button>
                </div>
            </form>
        </div>
    </section>
</x-guest-layout>
