@props(['product', 'showLoginCta' => true])

@php
    use Illuminate\Support\Str;
    $primaryImg = $product->primaryImage ?? $product->images->first();
    $image = $primaryImg?->image_path ? asset($primaryImg->image_path) : 'https://placehold.co/600x400?text=GameStation';

    // Lấy ảnh phụ đầu tiên (không phải ảnh chính)
    $allImages = $product->relationLoaded('images') ? $product->images : collect();
    $secondaryImg = $allImages->first(function ($img) use ($primaryImg) {
        return $img->id !== ($primaryImg?->id);
    });
    $hoverImage = $secondaryImg?->image_path ? asset($secondaryImg->image_path) : null;
@endphp

<div class="gs-product-card">
    <a href="{{ route('products.show', $product) }}" class="gs-product-image gs-product-image--hoverable">
        {{-- Ảnh chính --}}
        <img src="{{ $image }}" alt="{{ $product->name }}" class="gs-product-img gs-product-img--main">
        {{-- Ảnh phụ (hiện khi hover, chỉ render nếu có) --}}
        @if($hoverImage)
            <img src="{{ $hoverImage }}" alt="{{ $product->name }}" class="gs-product-img gs-product-img--hover" loading="lazy">
        @endif
    </a>
    <div class="gs-product-body">
        <h3 class="text-base font-semibold text-slate-900 line-clamp-2">
            <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
        </h3>
        @if($product->publisher)
            <div class="mt-1 text-xs text-slate-500">{{ $product->publisher->name }}</div>
        @endif
        <div class="mt-3">
            <span class="text-lg font-bold text-rose-600">{{ number_format($product->price, 0, ',', '.') }}đ</span>
        </div>
    </div>
    <div class="gs-product-actions mt-4">
        @if($showLoginCta)
            @auth
                <form method="POST" action="{{ route('cart.add') }}" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="gs-button px-3 py-2 text-sm rounded-full">Thêm</button>
                    <button type="submit" name="buy_now" value="1" class="gs-button gs-button--ghost px-3 py-2 text-sm rounded-full">Mua</button>
                </form>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login', ['next_action' => 'cart_add', 'product_id' => $product->id, 'quantity' => 1]) }}" class="gs-button px-3 py-2 text-sm rounded-full">Đăng nhập để mua</a>
                </div>
            @endauth
        @endif
    </div>
</div>
