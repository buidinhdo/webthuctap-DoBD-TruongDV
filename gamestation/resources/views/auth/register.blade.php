<x-guest-layout>
    <section class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="gs-auth-panel">
            <span class="gs-auth-kicker">Đăng ký</span>
            <h1 class="gs-auth-title">Tạo tài khoản GameStation</h1>
            <p class="gs-auth-subtitle">Nhập thông tin để tạo tài khoản và bắt đầu mua sắm.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-4">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Họ và tên')" />
                    <x-text-input id="name" class="mt-2 w-full gs-auth-input" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nhập họ và tên" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="mt-2 w-full gs-auth-input" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Nhập email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Mật khẩu')" />
                    <x-text-input id="password" class="mt-2 w-full gs-auth-input" type="password" name="password" required autocomplete="new-password" placeholder="Tối thiểu 6 ký tự" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />
                    <x-text-input id="password_confirmation" class="mt-2 w-full gs-auth-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-4">
                    <a class="gs-auth-link" href="{{ route('login') }}">Đã có tài khoản? Đăng nhập</a>
                    <x-primary-button class="gs-auth-button">
                        {{ __('Đăng ký') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </section>
</x-guest-layout>
