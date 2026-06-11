<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LuckySpinController extends Controller
{
    private $prizes = [
        0 => ['name' => 'Mã giảm giá 50k', 'type' => 'fixed', 'value' => 50000, 'min_order' => 500000],
        1 => ['name' => 'Mã giảm giá 100k', 'type' => 'fixed', 'value' => 100000, 'min_order' => 1000000],
        2 => ['name' => 'Miễn phí vận chuyển', 'type' => 'fixed', 'value' => 0, 'min_order' => 0],
        3 => ['name' => 'Mã giảm giá 200k', 'type' => 'fixed', 'value' => 200000, 'min_order' => 1500000],
        4 => ['name' => 'Chúc bạn may mắn lần sau', 'type' => 'lose', 'value' => 0, 'min_order' => 0],
        5 => ['name' => 'Mã giảm giá 300k', 'type' => 'fixed', 'value' => 300000, 'min_order' => 1800000],
        6 => ['name' => 'Mã giảm giá 150k', 'type' => 'fixed', 'value' => 150000, 'min_order' => 1200000],
        7 => ['name' => 'Mã giảm giá 500k', 'type' => 'fixed', 'value' => 500000, 'min_order' => 2000000],
    ];

    public function index()
    {
        $hasSpunToday = false;
        if (auth()->check()) {
            $hasSpunToday = UserNotification::where('user_id', auth()->id())
                ->where('title', 'Vòng quay may mắn')
                ->where('created_at', '>=', Carbon::today())
                ->exists();
        }

        return view('lucky-spin.index', compact('hasSpunToday'));
    }

    public function spin(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để tham gia vòng quay!'
            ], 401);
        }

        $userId = auth()->id();

        // Check if user has already spun today
        $hasSpunToday = UserNotification::where('user_id', $userId)
            ->where('title', 'Vòng quay may mắn')
            ->where('created_at', '>=', Carbon::today())
            ->exists();

        if ($hasSpunToday) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã quay hôm nay rồi. Hãy quay lại vào ngày mai nhé!'
            ], 422);
        }

        // Randomize prize index (0 to 7) with custom weight
        $weights = [
            0 => 25, // 50k
            1 => 20, // 100k
            2 => 15, // Freeship
            3 => 10, // 200k
            4 => 15, // Lose
            5 => 8,  // 300k
            6 => 6,  // 150k
            7 => 1,  // 500k
        ];

        $prizeIndex = $this->weightedRandom($weights);
        $prize = $this->prizes[$prizeIndex];

        $isWin = ($prizeIndex !== 4);
        $couponCode = null;

        if ($isWin) {
            if ($prizeIndex === 2) {
                // Freeship coupon
                $couponCode = 'LUCKYFREE-' . strtoupper(Str::random(6));
            } else {
                $couponCode = 'LUCKY' . ($prize['value'] / 1000) . 'K-' . strtoupper(Str::random(6));
            }

            Coupon::create([
                'code' => $couponCode,
                'type' => 'fixed',
                'value' => $prize['value'],
                'min_order' => $prize['min_order'],
                'usage_limit' => 1,
                'used_count' => 0,
                'starts_at' => now(),
                'ends_at' => now()->addDays(7), // valid for 7 days
                'is_active' => true,
            ]);

            $notificationBody = "Chúc mừng! Bạn đã quay trúng " . strtolower($prize['name']) . ": {$couponCode}";
        } else {
            $notificationBody = "Rất tiếc! Bạn đã quay trúng ô: Chúc bạn may mắn lần sau. Hãy quay lại vào ngày mai nhé!";
        }

        // Record the spin in notifications to prevent bypass
        UserNotification::create([
            'user_id' => $userId,
            'title' => 'Vòng quay may mắn',
            'body' => $notificationBody,
        ]);

        return response()->json([
            'success' => true,
            'prize_index' => $prizeIndex,
            'prize_name' => $prize['name'],
            'coupon_code' => $couponCode,
            'is_win' => $isWin
        ]);
    }

    private function weightedRandom(array $weights): int
    {
        $r = rand(1, array_sum($weights));
        $current = 0;
        foreach ($weights as $index => $weight) {
            $current += $weight;
            if ($r <= $current) {
                return $index;
            }
        }
        return 0;
    }
}
