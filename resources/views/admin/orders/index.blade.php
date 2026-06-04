@extends('admin.layout')

@section('title', 'Quản lý đơn hàng')
@section('page_title', 'Danh sách đơn hàng')
@section('breadcrumb', 'Đơn hàng')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Quản lý đơn hàng</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 10px">STT</th>
                    <th>Khách hàng</th>
                    <th>Email</th>
                    <th>Số tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th style="width: 150px">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td><strong>{{ $order->user->name ?? 'N/A' }}</strong></td>
                    <td>{{ $order->user->email ?? 'N/A' }}</td>
                    <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                    <td>
                        <span class="badge badge-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'info') }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info btn-xs">
                            <i class="fas fa-eye"></i> Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Không có đơn hàng nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        @php
            $lastPage = $orders->lastPage();
            $currentPage = $orders->currentPage();
            $pageNumbers = $orders->getUrlRange(1, $lastPage);
        @endphp
        <nav aria-label="Phân trang đơn hàng">
            <ul class="pagination pagination-sm mb-0 justify-content-center flex-wrap admin-pagination-numeric">
                @foreach ($pageNumbers as $page => $url)
                    <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>

    <style>
        .admin-pagination-numeric .page-link { min-width: 44px; text-align: center; }
        .admin-pagination-numeric .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }
    </style>
</div>
@endsection
