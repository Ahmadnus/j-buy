<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

/**
 * CartService handles cart mutations.
 * Mirrors Flutter CartController.addToCart() duplicate-detection logic.
 */
class CartService
{
    /**
     * Add a product to the user's cart.
     * Same (product_id, selected_size, selected_color) → increment quantity.
     * New combination → create new cart item with snapshot fields.
     */
    public function addItem(User $user, array $data): void
    {
        $product = Product::findOrFail($data['product_id']);

        $existing = CartItem::where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->where('selected_size',  $data['selected_size'])
            ->where('selected_color', $data['selected_color'])
            ->first();

        if ($existing) {
            $existing->increment('quantity', $data['quantity']);
            return;
        }

        CartItem::create([
            'user_id'        => $user->id,
            'product_id'     => $product->id,
            'name_ar'        => $product->name_ar,
            'price'          => $product->price,
            'currency'       => $product->currency,
            'image_url'      => $product->image_url,
            'selected_size'  => $data['selected_size'],
            'selected_color' => $data['selected_color'],
            'quantity'       => $data['quantity'],
        ]);
    }

    /**
     * Update quantity of a specific cart item.
     * Returns the item or null if not found / doesn't belong to user.
     */
    public function updateItem(User $user, int $cartItemId, int $quantity): ?CartItem
    {
        $item = CartItem::where('id', $cartItemId)
                        ->where('user_id', $user->id)
                        ->first();

        if (! $item) {
            return null;
        }

        $item->update(['quantity' => $quantity]);
        return $item;
    }

    /**
     * Remove a specific cart item.
     */
    public function removeItem(User $user, int $cartItemId): bool
    {
        return CartItem::where('id', $cartItemId)
                       ->where('user_id', $user->id)
                       ->delete() > 0;
    }

    /**
     * Clear all cart items for the user.
     */
    public function clearCart(User $user): void
    {
        CartItem::where('user_id', $user->id)->delete();
    }

    /**
     * Reload user with fresh cartItems for response.
     */
    public function loadCart(User $user): User
    {
        return $user->load('cartItems');
    }
}
