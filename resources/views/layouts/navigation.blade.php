<nav x-data="{ open: false }" class="gs-nav">
    <div class="gs-topbar">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 text-xs sm:px-6 lg:px-8">
            <p>Hotline: 0900 000 000</p>
            <div class="flex items-center gap-4">
                <span>Hỗ trợ 24/7</span>
                <span>Giao nhanh nội thành</span>
            </div>
        </div>
    </div>
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto" />
                        <span class="text-lg font-extrabold tracking-wide">GameStation</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden items-center gap-4 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Trang chủ
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        Sản phẩm
                    </x-nav-link>
                    <x-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                        Tin tức
                    </x-nav-link>
                    <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                        Liên hệ
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                <form method="GET" action="{{ route('products.index') }}" class="gs-search">
                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm sản phẩm" class="gs-search-input">
                </form>
                @auth
                    <!-- Notification Icon -->
                    <div class="relative" x-data="{ notificationOpen: false }">
                        <button @click="notificationOpen = !notificationOpen" class="gs-icon-button relative" aria-label="Thông báo">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0018 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if(Auth::user()->userNotifications()->whereNull('read_at')->count() > 0)
                                <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div @click.away="notificationOpen = false"
                             x-show="notificationOpen"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                            <div class="p-4 border-b border-slate-200">
                                <h3 class="font-semibold text-slate-900">Thông báo</h3>
                            </div>
                            @php
                                $notifications = Auth::user()->userNotifications()->latest()->take(10)->get();
                            @endphp
                            @if($notifications->isEmpty())
                                <div class="p-8 text-center text-slate-500 text-sm">Không có thông báo mới</div>
                            @else
                                @foreach($notifications as $notification)
                                    <form method="POST" action="{{ route('notifications.read', $notification) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="block w-full text-left p-4 hover:bg-slate-50 border-b border-slate-100 {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50' }}">
                                            <p class="font-semibold text-slate-900 text-sm">{{ $notification->title }}</p>
                                            <p class="text-slate-600 text-xs mt-1 whitespace-pre-line">{{ $notification->body }}</p>
                                            <p class="text-slate-400 text-xs mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                        </button>
                                    </form>
                                @endforeach
                            @endif
                            @if($notifications->count() > 0)
                                <div class="p-3 border-t border-slate-200 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Xem tất cả</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endauth
                <a href="{{ route('cart.index') }}" class="gs-icon-button" aria-label="Giỏ hàng">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.6 3M7 13h10l3-7H6.6M7 13l-1.5 7h13L17 13M7 13h10" />
                    </svg>
                </a>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-600 bg-white/70 hover:text-slate-900 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Hồ sơ
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('orders.index')">
                                Đơn hàng
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    Đăng xuất
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="ms-4 gs-button">Đăng ký</a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="px-4 pt-3">
            <form method="GET" action="{{ route('products.index') }}" class="gs-search">
                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm sản phẩm" class="gs-search-input">
                <button type="submit" class="gs-search-button">Tìm</button>
            </form>
        </div>
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                Trang chủ
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                Sản phẩm
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                Tin tức
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                Liên hệ
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                    Giỏ hàng
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                    Thông báo
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Hồ sơ
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('orders.index')">
                        Đơn hàng
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            Đăng xuất
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-4 border-t border-gray-200 space-y-2 px-4">
                <a href="{{ route('login') }}" class="block text-sm font-semibold text-slate-700">Đăng nhập</a>
                <a href="{{ route('register') }}" class="block text-sm font-semibold text-sky-600">Đăng ký</a>
            </div>
        @endauth
    </div>
</nav>
