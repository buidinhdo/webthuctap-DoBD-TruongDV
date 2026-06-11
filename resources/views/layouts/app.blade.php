<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|ibm-plex-serif:400,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Temporary override removed; using compiled CSS in public/build/assets/app-*.css -->
    </head>
    <body class="antialiased">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @auth
                @php
                    $hasSpunToday = \App\Models\UserNotification::where('user_id', auth()->id())
                        ->where('title', 'Vòng quay may mắn')
                        ->where('created_at', '>=', \Carbon\Carbon::today())
                        ->exists();
                @endphp
                @if(!$hasSpunToday)
                    <!-- Premium Lucky Spin Alert Bar -->
                    <div class="py-3 px-4 shadow-md text-sm font-semibold tracking-wide transition-all relative overflow-hidden" style="background: linear-gradient(to right, #8b5cf6, #3b82f6); color: #ffffff;" x-data="{ showBar: true }" x-show="showBar">
                        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4 relative z-10">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">🎁</span>
                                <span>Bạn có <span class="font-bold" style="color: #fcd34d;">1 lượt quay may mắn</span> chưa sử dụng hôm nay! Hãy thử vận may nhận mã giảm giá lên tới 500k.</span>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <a href="{{ route('lucky-spin.index') }}" class="px-4 py-1.5 rounded-full text-xs font-extrabold tracking-wide uppercase transition shadow hover:scale-105 no-underline" style="background-color: #ffffff; color: #4f46e5; display: inline-block;">
                                    QUAY NGAY
                                </a>
                                <button @click="showBar = false" class="transition-colors p-1" style="color: rgba(255, 255, 255, 0.8); background: none; border: none; cursor: pointer; display: flex; align-items: center;" aria-label="Đóng thông báo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            @include('partials.flash')

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            @include('layouts.footer')
        </div>
    </body>

    {{-- Touch handler: hiện ảnh phụ khi chạm vào sản phẩm trên mobile --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('touchstart', function (e) {
                var imgEl = e.target.closest('.gs-product-image--hoverable');
                // Xóa class is-touched trên các card khác
                document.querySelectorAll('.gs-product-image--hoverable.is-touched').forEach(function (el) {
                    if (el !== imgEl) el.classList.remove('is-touched');
                });
                if (imgEl) {
                    imgEl.classList.toggle('is-touched');
                }
            }, { passive: true });
        });
    </script>
</html>
