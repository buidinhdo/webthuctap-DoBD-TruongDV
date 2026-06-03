@if (session('success'))
    <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="gs-card border-l-4 border-emerald-400 bg-emerald-50/80 p-4 text-emerald-700">
            {{ session('success') }}
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="gs-card border-l-4 border-rose-400 bg-rose-50/80 p-4 text-rose-700">
            {{ session('error') }}
        </div>
    </div>
@endif
