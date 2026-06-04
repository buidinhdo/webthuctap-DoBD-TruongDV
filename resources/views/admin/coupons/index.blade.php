@extends('admin.layout')

@section('title', 'Quản lý mã giảm giá')
@section('page_title', 'Quản lý mã giảm giá')
@section('breadcrumb', 'Quản lý mã giảm giá')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách mã giảm giá</h3>
        <div class="card-tools">
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Thêm mã giảm giá
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($coupons->count())
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Đơn tối thiểu</th>
                        <th>Đã dùng / Giới hạn</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày hết hạn</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->id }}</td>
                        <td><strong>{{ $coupon->code }}</strong></td>
                        <td>
                            @if($coupon->type === 'percentage')
                                <span class="badge badge-info">Phần trăm</span>
                            @else
                                <span class="badge badge-secondary">Cố định</span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->type === 'percentage')
                                {{ $coupon->value }}%
                            @else
                                {{ number_format($coupon->value, 0, ',', '.') }}đ
                            @endif
                        </td>
                        <td>{{ $coupon->min_order ? number_format($coupon->min_order, 0, ',', '.') . 'đ' : '—' }}</td>
                        <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                        <td>{{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y') : '—' }}</td>
                        <td>{{ $coupon->ends_at ? $coupon->ends_at->format('d/m/Y') : '—' }}</td>
                        <td>
                            @if($coupon->is_active)
                                <span class="badge badge-success">Hoạt động</span>
                            @else
                                <span class="badge badge-danger">Tắt</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $coupons->links() }}
        </div>
        @else
        <div class="alert alert-info">
            Chưa có mã giảm giá nào. <a href="{{ route('admin.coupons.create') }}">Thêm mới</a>
        </div>
        @endif
    </div>
</div>
@endsection