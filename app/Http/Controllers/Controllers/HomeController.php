<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Article;

class HomeController extends Controller
{
    public function index()
    {
        // Use ordered banner list from admin settings file.
        $orderFile = storage_path('app/banner_order.json');
        $orderMap = file_exists($orderFile)
            ? (json_decode(file_get_contents($orderFile), true) ?: [])
            : [];

        $banners = collect(is_dir(public_path('images/banners')) ? array_diff(scandir(public_path('images/banners')), ['.', '..']) : [])
            ->sort(function ($a, $b) use ($orderMap) {
                $orderA = (int) ($orderMap[$a] ?? PHP_INT_MAX);
                $orderB = (int) ($orderMap[$b] ?? PHP_INT_MAX);

                if ($orderA === $orderB) {
                    return strcasecmp($a, $b);
                }

                return $orderA <=> $orderB;
            })
            ->values()
            ->map(fn ($file) => '/images/banners/'.$file)
            ->values();

        $categoryMap = Category::whereIn('slug', ['ps4', 'ps5', 'switch'])->pluck('id', 'slug');

        $featured = Product::with(['primaryImage', 'images', 'category'])
            ->where('is_active', true)
            ->where('featured', true)
            ->latest()
            ->take(10)
            ->get();

        $latest = Product::with(['primaryImage', 'images', 'category'])
            ->where('is_active', true)
            ->latest()
            ->take(10)
            ->get();

        $ps4 = Product::with(['primaryImage', 'images', 'category'])
            ->where('is_active', true)
            ->when($categoryMap->get('ps4'), fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->latest()
            ->take(100)
            ->get();

        $ps5 = Product::with(['primaryImage', 'images', 'category'])
            ->where('is_active', true)
            ->when($categoryMap->get('ps5'), fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->latest()
            ->take(100)
            ->get();

        $switch = Product::with(['primaryImage', 'images', 'category'])
            ->where('is_active', true)
            ->when($categoryMap->get('switch'), fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->latest()
            ->take(100)
            ->get();

        $articles = Article::where('is_published', true)
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('home', compact('banners', 'featured', 'latest', 'ps4', 'ps5', 'switch', 'articles'));
    }
}
