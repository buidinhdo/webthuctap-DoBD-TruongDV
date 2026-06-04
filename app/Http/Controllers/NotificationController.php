<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->syncContactReplyNotifications($request->user());

        $notifications = UserNotification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, UserNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->userNotifications()->whereNull('read_at')->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }

    private function syncContactReplyNotifications($user): void
    {
        $email = mb_strtolower(trim((string) $user->email));

        $contacts = ContactMessage::query()
            ->whereNotNull('admin_reply')
            ->where('status', 'replied')
            ->where(function ($q) use ($user, $email) {
                $q->where('user_id', $user->id)
                    ->orWhereRaw('LOWER(email) = ?', [$email]);
            })
            ->latest('admin_replied_at')
            ->get();

        foreach ($contacts as $contact) {
            $exists = $user->userNotifications()
                ->where('title', 'Phản hồi từ quản lý liên hệ')
                ->where('body', 'like', "Liên hệ #{$contact->id}%")
                ->exists();

            if ($exists) {
                continue;
            }

            $user->userNotifications()->create([
                'title' => 'Phản hồi từ quản lý liên hệ',
                'body' => "Liên hệ #{$contact->id}\nChủ đề: {$contact->subject}\n\nNội dung phản hồi: {$contact->admin_reply}",
            ]);
        }
    }
}
