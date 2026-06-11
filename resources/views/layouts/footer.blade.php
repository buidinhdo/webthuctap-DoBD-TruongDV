<footer class="gs-footer">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-4">
            <div>
                <div class="flex items-center gap-2">
                    <x-application-logo class="h-9 w-auto" />
                    <span class="text-lg font-extrabold tracking-wide">GameStation</span>
                </div>
                <p class="mt-4 text-sm text-slate-300">
                    Hệ thống bán lẻ game và phụ kiện chính hãng. Cập nhật sản phẩm mới mỗi ngày.
                </p>
                <p class="mt-4 text-sm text-slate-300">Hotline: 0900 000 000</p>
                <p class="text-sm text-slate-300">Email: support@gamestation.test</p>
            </div>
            <div>
                <h3 class="gs-footer-title">Danh mục</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('products.index', ['category' => 'ps5']) }}" class="gs-footer-link">PlayStation 5</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'ps4']) }}" class="gs-footer-link">PlayStation 4</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'switch']) }}" class="gs-footer-link">Nintendo Switch</a></li>
                </ul>
            </div>
            <div>
                <h3 class="gs-footer-title">Hỗ trợ</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('contact') }}" class="gs-footer-link">Liên hệ</a></li>
                    <li><a href="{{ route('products.index') }}" class="gs-footer-link">Sản phẩm</a></li>
                    <li><a href="{{ route('orders.index') }}" class="gs-footer-link">Đơn hàng</a></li>
                </ul>
            </div>
            <div>
                <h3 class="gs-footer-title">Thông tin cửa hàng</h3>
                <p class="mt-4 text-sm text-slate-300">HCM: 123 Nguyễn Huệ TP.HCM</p>
                <p class="mt-2 text-sm text-slate-300">HN: 88 Consoles Road, Hai Bà Trưng</p>
                <div class="mt-4 flex gap-3">
                    <span class="gs-footer-badge">Giao nhanh</span>
                    <span class="gs-footer-badge">Chính hãng</span>
                </div>
            </div>
        </div>
        <div class="mt-10 border-t border-slate-700/60 pt-6 text-xs text-slate-400">
            © 2026 GameStation. Bản quyền thuộc về GameStation.
        </div>
    </div>
</footer>
