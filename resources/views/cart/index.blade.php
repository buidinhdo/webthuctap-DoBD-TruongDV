<x-app-layout>
    @php
        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-semibold text-slate-900">Giỏ hàng</h1>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                @forelse ($cart->items as $item)
                    <div class="gs-card flex flex-wrap items-center gap-4 p-4">
                        <img src="{{ $item->product->primaryImage?->image_path ? asset($item->product->primaryImage->image_path) : 'https://placehold.co/120x120' }}" alt="{{ $item->product->name }}" class="h-20 w-20 rounded-xl object-cover">
                        <div class="flex-1">
                            <p class="text-lg font-semibold text-slate-900">{{ $item->product->name }}</p>
                            <p class="text-sm text-slate-600">{{ number_format($item->price, 0, ',', '.') }}đ</p>
                        </div>
                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" class="w-20 rounded-xl border-slate-200" />
                            <button class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Cập nhật</button>
                        </form>
                        <form method="POST" action="{{ route('cart.remove', $item) }}">
                            @csrf
                            <button class="text-sm font-semibold text-rose-500">Xóa</button>
                        </form>
                    </div>
                @empty
                    <div class="gs-card p-6 text-slate-600">Giỏ hàng đang trống.</div>
                @endforelse
            </div>

            <div class="space-y-4">
                <div class="gs-card p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Tổng kết</h3>
                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Tạm tính</span>
                            <span class="font-semibold text-slate-900">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Thanh toán</a>
                </div>

                <div class="gs-card p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Mã giảm giá</h3>
                    <form method="POST" action="{{ route('cart.coupon') }}" class="mt-4 flex gap-2">
                        @csrf
                        <input type="text" name="coupon" placeholder="Nhập mã" class="w-full rounded-xl border-slate-200" />
                        <button class="gs-button" type="submit">Áp dụng</button>
                    </form>
                    @if (session('coupon_code'))
                        <p class="mt-3 text-xs text-slate-500">Đang áp dụng: {{ session('coupon_code') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
