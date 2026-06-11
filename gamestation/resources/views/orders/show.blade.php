<x-app-layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold text-slate-900">Đơn hàng #{{ $order->id }}</h1>
            <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-slate-600">Quay lại</a>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                @foreach ($order->items as $item)
                    <div class="gs-card flex items-center gap-4 p-4">
                        <img src="{{ $item->product->primaryImage?->image_path ? asset($item->product->primaryImage->image_path) : 'https://placehold.co/120x120' }}" alt="{{ $item->product_name }}" class="h-20 w-20 rounded-xl object-cover">
                        <div class="flex-1">
                            <p class="text-lg font-semibold text-slate-900">{{ $item->product_name }}</p>
                            <p class="text-sm text-slate-600">Số lượng: {{ $item->quantity }}</p>
                        </div>
                            <span class="text-sm font-semibold text-slate-900">{{ number_format($item->total, 0, ',', '.') }}đ</span>
                    </div>
                @endforeach
            </div>

            <div class="space-y-4">
                <div class="gs-card p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Tổng kết</h3>
                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Tạm tính</span>
                            <span class="font-semibold text-slate-900">{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Giảm giá</span>
                            <span class="font-semibold text-emerald-600">-{{ number_format($order->discount, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Phí giao hàng</span>
                            <span class="font-semibold text-slate-900">{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Khoảng cách giao hàng</span>
                            <span class="font-semibold text-slate-900">{{ number_format((float)($order->shipping_distance_km ?? 0), 2, ',', '.') }} km</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200 pt-2">
                            <span class="font-semibold">Tổng cộng</span>
                            <span class="text-lg font-bold text-slate-900">{{ number_format($order->total, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>

                <div class="gs-card p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Giao hàng</h3>
                    <p class="mt-3 text-sm text-slate-600">{{ $order->shipping_name }}</p>
                    <p class="text-sm text-slate-600">{{ $order->shipping_phone }}</p>
                    <p class="text-sm text-slate-600">{{ $order->shipping_address }}</p>
                    <p class="mt-2 text-sm text-slate-600">
                        Phương thức: <span class="font-semibold text-slate-900">{{ $order->shipping_method === 'express' ? 'Giao hàng nhanh (24h-48h)' : 'Giao hàng tiêu chuẩn (2-4 ngày)' }}</span>
                    </p>
                    <p class="mt-2 text-sm text-slate-600">
                        Trạng thái đơn: <span class="font-semibold text-slate-900">{{ $order->status_label }}</span>
                    </p>
                    <p class="mt-1 text-sm text-slate-600">
                        Thanh toán: <span class="font-semibold text-slate-900">{{ $order->payment_status_label }}</span>
                    </p>
                    <p class="text-xs text-slate-500 mt-3">Shop: {{ config('shipping.shop_address') }}</p>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
