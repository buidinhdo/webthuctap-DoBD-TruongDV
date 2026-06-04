<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserNotification;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::with('items.product.primaryImage')->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $coupon = $this->resolveCoupon($request, $cart);

        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $discount = 0;
        if ($coupon) {
            $discount = $coupon->type === 'percent'
                ? round($subtotal * ($coupon->value / 100), 2)
                : (float) $coupon->value;
        }

        // Default initial shipping fee
        $initialShippingFee = $coupon ? 0.0 : $this->calculateShippingFee(5.0, false, 'standard');
        $initialTotal = max(0, $subtotal - $discount) + $initialShippingFee;

        return view('checkout.index', compact(
            'cart',
            'coupon',
            'initialShippingFee',
            'initialTotal'
        ));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:30'],
            'province' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'ward' => ['required', 'string', 'max:255'],
            'detail' => ['required', 'string', 'max:255'],
            'shipping_method' => ['required', 'string', 'in:standard,express'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        
        $province = $request->input('province');
        $district = $request->input('district');
        $ward = $request->input('ward');
        $detail = $request->input('detail');
        $fullAddress = "{$detail}, {$ward}, {$district}, {$province}";
        
        $data = [
            'shipping_name' => $request->input('shipping_name'),
            'shipping_phone' => $request->input('shipping_phone'),
            'shipping_address' => $fullAddress,
            'shipping_method' => $request->input('shipping_method'),
            'payment_method' => $request->input('payment_method'),
            'notes' => $request->input('notes'),
        ];

        $cart = Cart::with('items.product')->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Gio hang dang trong.');
        }

        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->with('error', "San pham {$item->product->name} khong du ton kho.");
            }
        }

        $coupon = $this->resolveCoupon($request, $cart);

        $order = DB::transaction(function () use ($request, $cart, $coupon, $data) {
            $subtotal = $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $discount = 0;
            if ($coupon) {
                $discount = $coupon->type === 'percent'
                    ? round($subtotal * ($coupon->value / 100), 2)
                    : (float) $coupon->value;
            }

            $shop = $this->resolveShopLocation();
            $customer = $this->geocodeAddress($data['shipping_address']);
            $distanceKm = $this->resolveDistanceKm($shop, $customer);
            $shippingFee = $this->calculateShippingFee($distanceKm, $coupon !== null, $data['shipping_method']);
            $total = max(0, $subtotal - $discount) + $shippingFee;

            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shippingFee,
                'shipping_distance_km' => round($distanceKm, 2),
                'total' => $total,
                'coupon_code' => $coupon?->code,
                'shipping_name' => $data['shipping_name'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_method' => $data['shipping_method'],
                'notes' => $data['notes'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                Product::where('id', $item->product_id)
                    ->decrement('stock', $item->quantity);
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $cart->items()->delete();
            $request->session()->forget('coupon_code');

            UserNotification::create([
                'user_id' => $request->user()->id,
                'title' => 'Đơn hàng mới',
                'body' => "Đơn hàng #{$order->id} đã được tạo thành công.",
            ]);

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Đặt hàng thành công.');
    }

    public function calculateShippingFeeApi(Request $request)
    {
        $request->validate([
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_method' => ['required', 'string', 'in:standard,express'],
        ]);

        $cart = Cart::with('items.product')->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $coupon = $this->resolveCoupon($request, $cart);
        $discount = 0;
        if ($coupon) {
            $discount = $coupon->type === 'percent'
                ? round($subtotal * ($coupon->value / 100), 2)
                : (float) $coupon->value;
        }

        $shop = $this->resolveShopLocation();
        $customer = $this->geocodeAddress($request->shipping_address);
        $distanceKm = $this->resolveDistanceKm($shop, $customer);
        $shippingFee = $this->calculateShippingFee($distanceKm, $coupon !== null, $request->shipping_method);
        $total = max(0, $subtotal - $discount) + $shippingFee;

        return response()->json([
            'success' => true,
            'distance_km' => round($distanceKm, 2),
            'shipping_fee' => $shippingFee,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'formatted_shipping_fee' => number_format($shippingFee, 0, ',', '.') . 'đ',
            'formatted_total' => number_format($total, 0, ',', '.') . 'đ',
            'formatted_discount' => number_format($discount, 0, ',', '.') . 'đ',
        ]);
    }

    protected function resolveCoupon(Request $request, Cart $cart): ?Coupon
    {
        $code = $request->session()->get('coupon_code');
        if (!$code) {
            return null;
        }

        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon || !$coupon->isValidForAmount($subtotal)) {
            $request->session()->forget('coupon_code');
            return null;
        }

        return $coupon;
    }

    private function resolveShopLocation(): array
    {
        $configuredLat = config('shipping.shop_lat');
        $configuredLng = config('shipping.shop_lng');

        if ($configuredLat !== null && $configuredLng !== null) {
            return [
                'lat' => (float) $configuredLat,
                'lng' => (float) $configuredLng,
                'address' => (string) config('shipping.shop_address'),
            ];
        }

        $shopAddress = (string) config('shipping.shop_address');
        $geo = $this->geocodeAddress($shopAddress);

        return [
            'lat' => $geo['lat'] ?? 0.0,
            'lng' => $geo['lng'] ?? 0.0,
            'address' => $shopAddress,
        ];
    }

    private function geocodeAddress(string $address): array
    {
        $result = $this->queryNominatim($address);
        if ($result) {
            return $result;
        }

        $parts = array_map('trim', explode(',', $address));
        while (count($parts) > 1) {
            array_shift($parts);
            $subAddress = implode(', ', $parts);
            $result = $this->queryNominatim($subAddress);
            if ($result) {
                return $result;
            }
        }

        return [];
    }

    private function queryNominatim(string $query): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'GameStation/1.0'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                ]);

            if (!$response->successful()) {
                return [];
            }

            $first = $response->json()[0] ?? null;
            if (!$first) {
                return [];
            }

            return [
                'lat' => isset($first['lat']) ? (float) $first['lat'] : null,
                'lng' => isset($first['lon']) ? (float) $first['lon'] : null,
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function resolveDistanceKm(array $shop, array $customer): float
    {
        $shopLat = $shop['lat'] ?? null;
        $shopLng = $shop['lng'] ?? null;
        $customerLat = $customer['lat'] ?? null;
        $customerLng = $customer['lng'] ?? null;

        if ($shopLat === null || $shopLng === null || $customerLat === null || $customerLng === null || ($shopLat == 0.0 && $shopLng == 0.0)) {
            return (float) config('shipping.fallback_distance_km', 5);
        }

        $routeKm = $this->fetchDrivingDistanceKm((float) $shopLat, (float) $shopLng, (float) $customerLat, (float) $customerLng);
        if ($routeKm !== null) {
            return $routeKm;
        }

        return $this->haversineDistanceKm((float) $shopLat, (float) $shopLng, (float) $customerLat, (float) $customerLng);
    }

    private function fetchDrivingDistanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): ?float
    {
        try {
            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%F,%F;%F,%F',
                $fromLng,
                $fromLat,
                $toLng,
                $toLat
            );

            $response = Http::timeout(8)->get($url, [
                'overview' => 'false',
                'alternatives' => 'false',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $meters = data_get($response->json(), 'routes.0.distance');
            if (!is_numeric($meters)) {
                return null;
            }

            return max(0.1, round(((float) $meters) / 1000, 2));
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function haversineDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return max(0.1, round($earthRadius * $c, 2));
    }

    private function calculateShippingFee(float $distanceKm, bool $hasCoupon, string $shippingMethod): float
    {
        if ($hasCoupon && (bool) config('shipping.free_with_coupon', true)) {
            return 0;
        }

        $minFee = (float) config('shipping.min_fee', 15000);
        $feePerKm = (float) config('shipping.fee_per_km', 3500);
        $expressSurcharge = (float) config('shipping.express_surcharge', 15000);

        $distanceFee = max($minFee, ceil($distanceKm) * $feePerKm);

        if ($shippingMethod === 'express') {
            $distanceFee += $expressSurcharge;
        }

        return round($distanceFee, 2);
    }
}
