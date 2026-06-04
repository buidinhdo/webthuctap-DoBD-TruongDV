<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $orderMap = $this->readOrderMap();
        $banners = $this->getSortedBannerFiles($orderMap)
            ->map(fn ($filename) => [
                'filename' => $filename,
                'order' => (int) ($orderMap[$filename] ?? 0),
            ]);

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/banners'), $filename);

            $orderMap = $this->readOrderMap();
            if (!isset($orderMap[$filename])) {
                $orderMap[$filename] = empty($orderMap) ? 1 : (max($orderMap) + 1);
            }
            $this->writeOrderMap($orderMap);

            return redirect()->route('admin.banners.index')->with('success', 'Banner đã được thêm.');
        }

        return back()->with('error', 'Lỗi khi tải ảnh.');
    }

    public function edit($filename)
    {
        $path = public_path('images/banners/' . $filename);
        if (!file_exists($path)) {
            return back()->with('error', 'Banner không tồn tại.');
        }
        return view('admin.banners.edit', compact('filename'));
    }

    public function update(Request $request, $filename)
    {
        $oldPath = public_path('images/banners/' . $filename);
        if (!file_exists($oldPath)) {
            return back()->with('error', 'Banner không tồn tại.');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->file('image')) {
            $file = $request->file('image');
            $newFilename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/banners'), $newFilename);

            $orderMap = $this->readOrderMap();
            $existingOrder = $orderMap[$filename] ?? null;
            unset($orderMap[$filename]);
            $orderMap[$newFilename] = $existingOrder ?? (empty($orderMap) ? 1 : (max($orderMap) + 1));
            $this->writeOrderMap($orderMap);
            
            // Delete old file
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            
            return redirect()->route('admin.banners.index')->with('success', 'Banner đã được cập nhật.');
        }

        return back()->with('error', 'Lỗi khi tải ảnh.');
    }

    public function updateOrder(Request $request, $filename)
    {
        $path = public_path('images/banners/' . $filename);
        if (!file_exists($path)) {
            return back()->with('error', 'Banner không tồn tại.');
        }

        $validated = $request->validate([
            'order' => 'required|integer|min:1|max:9999',
        ]);

        $orderMap = $this->readOrderMap();
        $orderMap[$filename] = (int) $validated['order'];
        $this->writeOrderMap($orderMap);

        return redirect()->route('admin.banners.index')->with('success', 'Đã cập nhật thứ tự banner.');
    }

    public function destroy($filename)
    {
        $path = public_path('images/banners/' . $filename);
        if (file_exists($path)) {
            unlink($path);

            $orderMap = $this->readOrderMap();
            unset($orderMap[$filename]);
            $this->writeOrderMap($orderMap);

            return redirect()->route('admin.banners.index')->with('success', 'Banner đã được xóa.');
        }
        return back()->with('error', 'Banner không tồn tại.');
    }

    private function readOrderMap(): array
    {
        $orderFile = storage_path('app/banner_order.json');
        if (!file_exists($orderFile)) {
            return [];
        }

        $decoded = json_decode(file_get_contents($orderFile), true);
        return is_array($decoded) ? $decoded : [];
    }

    private function writeOrderMap(array $orderMap): void
    {
        $orderFile = storage_path('app/banner_order.json');

        if (!is_dir(dirname($orderFile))) {
            mkdir(dirname($orderFile), 0755, true);
        }

        file_put_contents($orderFile, json_encode($orderMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function getSortedBannerFiles(array $orderMap)
    {
        $bannerDir = public_path('images/banners');
        $files = collect(is_dir($bannerDir) ? array_diff(scandir($bannerDir), ['.', '..']) : []);

        return $files->sort(function ($a, $b) use ($orderMap) {
            $orderA = (int) ($orderMap[$a] ?? PHP_INT_MAX);
            $orderB = (int) ($orderMap[$b] ?? PHP_INT_MAX);

            if ($orderA === $orderB) {
                return strcasecmp($a, $b);
            }

            return $orderA <=> $orderB;
        })->values();
    }
}
