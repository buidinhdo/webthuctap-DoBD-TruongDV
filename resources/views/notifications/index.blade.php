<x-app-layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-semibold text-slate-900">Thông báo</h1>

        <div class="mt-6 space-y-4">
            @forelse ($notifications as $notification)
                <div class="gs-card p-5 {{ $notification->read_at ? 'opacity-70' : '' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-lg font-semibold text-slate-900">{{ $notification->title }}</p>
                            <p class="mt-2 text-sm text-slate-600 whitespace-pre-line">{{ $notification->body }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        @if (!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                <button class="text-xs font-semibold text-sky-600">Đánh dấu đã đọc</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="gs-card p-6 text-slate-600">Chưa có thông báo nào.</div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    </section>
</x-app-layout>
