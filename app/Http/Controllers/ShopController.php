<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShopController extends Controller
{
    /**
     * Display all available products.
     */
    public function index(Request $request)
    {
        $query = Product::where('is_available', true)
            ->with('photo');

        // Filter by type if provided
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        // Get unique product types for filtering
        $types = Product::where('is_available', true)
            ->distinct()
            ->pluck('type');

        return Inertia::render('Shop/Index', [
            'products' => $products,
            'types' => $types,
            'filters' => $request->only(['type', 'search']),
        ]);
    }

    /**
     * Display a single product.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_available', true)
            ->with('photo.album')
            ->firstOrFail();

        return Inertia::render('Shop/Show', [
            'product' => $product,
        ]);
    }
}
