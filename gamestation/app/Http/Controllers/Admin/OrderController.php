<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $status = request()->query('status');
        $allowedStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        $orders = Order::with('user')
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate(15)
            ->appends(request()->query());

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product.primaryImage', 'items.product.images');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);

        if ($validated['status'] === 'completed' && ! $order->completed_at) {
            $validated['completed_at'] = now();
        }

        $oldStatus = $order->status;
        $order->update($validated);

        if ($oldStatus !== $order->status) {
            \App\Models\UserNotification::create([
                'user_id' => $order->user_id,
                'title' => 'Cập nhật trạng thái đơn hàng',
                'body' => "Đơn hàng #{$order->id} đã thay đổi trạng thái sang: " . $order->status_label,
            ]);
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }
}
