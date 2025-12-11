<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();

        $cartItems = CartItem::where('session_id', $sessionId)
            ->with('product')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if product is available and has sufficient stock
        if (!$product->is_available || $product->stock < $validated['quantity']) {
            return back()->with('error', 'Product is not available or insufficient stock.');
        }

        $sessionId = $request->session()->getId();

        // Check if item already exists in cart
        $cartItem = CartItem::where('session_id', $sessionId)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            // Update quantity if already in cart
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            if ($product->stock < $newQuantity) {
                return back()->with('error', 'Insufficient stock.');
            }

            $cartItem->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            // Create new cart item
            CartItem::create([
                'session_id' => $sessionId,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'price' => $product->price,
            ]);
        }

        return back()->with('success', 'Product added to cart.');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Verify the cart item belongs to this session
        if ($cartItem->session_id !== $request->session()->getId()) {
            abort(403);
        }

        // Check stock availability
        if ($cartItem->product->stock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock.');
        }

        $cartItem->update($validated);

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, CartItem $cartItem)
    {
        // Verify the cart item belongs to this session
        if ($cartItem->session_id !== $request->session()->getId()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}
