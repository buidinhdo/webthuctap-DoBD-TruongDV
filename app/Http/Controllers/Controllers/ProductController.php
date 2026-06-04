<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['primaryImage', 'images', 'category'])
            ->where('is_active', true);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->string('category')->trim()->toString()) {
            $query->whereHas('category', function ($builder) use ($categorySlug) {
                $builder->where('slug', $categorySlug);
            });
        }

        if ($platform = $request->string('platform')->trim()->toString()) {
            $query->where('platform', $platform);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        if ($esrb = $request->string('esrb')->trim()->toString()) {
            $query->where('esrb', $esrb);
        }

        if ($publisherId = $request->integer('publisher')) {
            $query->where('publisher_id', $publisherId);
        }

        if ($genre = $request->string('genre')->trim()->toString()) {
            $query->where('genre', $genre);
        }

        $sort = $request->string('sort')->toString();
        if ($sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($sort === 'price_desc') {
            $query->orderByDesc('price');
        } elseif ($sort === 'bestseller') {
            $query->orderByDesc('popularity');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();
        $publishers = \App\Models\Publisher::orderBy('name')->get();
        $genres = Genre::orderBy('name')
            ->pluck('name')
            ->filter()
            ->values();

        return view('products.index', compact('products', 'categories', 'publishers', 'genres'));
    }

    public function show(Product $product)
    {
        $product->load(['images', 'primaryImage', 'category', 'reviews.user']);
        $product->thumbnailImage = $product->primaryImage ?? $product->images->first();
        
        $related = Product::with('primaryImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
