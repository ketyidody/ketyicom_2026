<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';

const props = defineProps({
    cartItems: Array,
    total: Number,
});

const updateQuantity = (cartItemId, quantity) => {
    router.patch(route('cart.update', cartItemId), {
        quantity: quantity,
    }, {
        preserveScroll: true,
    });
};

const removeItem = (cartItemId) => {
    if (confirm('Remove this item from your cart?')) {
        router.delete(route('cart.destroy', cartItemId), {
            preserveScroll: true,
        });
    }
};

const incrementQuantity = (item) => {
    const newQuantity = item.quantity + 1;
    if (newQuantity <= item.product.stock) {
        updateQuantity(item.id, newQuantity);
    }
};

const decrementQuantity = (item) => {
    const newQuantity = item.quantity - 1;
    if (newQuantity >= 1) {
        updateQuantity(item.id, newQuantity);
    }
};
</script>

<template>
    <Head title="Shopping Cart" />

    <PublicLayout>
        <!-- Page Header -->
        <div class="border-b border-gray-200 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center">
                    <h1 class="text-4xl md:text-5xl font-light tracking-wide text-gray-900">CART</h1>
                    <Link :href="route('shop.index')" class="text-sm text-gray-600 hover:text-gray-900 tracking-wide">
                        CONTINUE SHOPPING
                    </Link>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-7xl mx-auto">
                <div v-if="$page.props.flash?.success" class="border border-green-800 bg-green-900 bg-opacity-20 p-4 mb-4">
                    <p class="text-sm text-green-400">{{ $page.props.flash.success }}</p>
                </div>
                <div v-if="$page.props.flash?.error" class="border border-red-800 bg-red-900 bg-opacity-20 p-4">
                    <p class="text-sm text-red-400">{{ $page.props.flash.error }}</p>
                </div>
            </div>
        </div>

        <!-- Cart Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-7xl mx-auto">
                <!-- Empty Cart -->
                <div v-if="cartItems.length === 0" class="text-center py-24">
                    <svg class="mx-auto h-24 w-24 text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="text-2xl font-light text-gray-700 mb-4">YOUR CART IS EMPTY</h3>
                    <p class="text-gray-500 mb-8">Start shopping to add items to your cart</p>
                    <Link
                        :href="route('shop.index')"
                        class="inline-block bg-black text-white px-8 py-3 text-sm font-light tracking-wider hover:bg-gray-800 transition-colors"
                    >
                        BROWSE PRODUCTS
                    </Link>
                </div>

                <!-- Cart Items -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Items List -->
                    <div class="lg:col-span-2 space-y-6">
                        <div
                            v-for="item in cartItems"
                            :key="item.id"
                            class="border border-gray-200 p-6"
                        >
                            <div class="flex gap-6">
                                <!-- Product Image -->
                                <div class="flex-shrink-0 w-32 h-32 bg-gray-100 overflow-hidden">
                                    <LazyImage
                                        v-if="item.product.image"
                                        :src="`/storage/${item.product.image}`"
                                        :alt="item.product.name"
                                        class-name="w-full h-full object-cover"
                                    />
                                    <LazyImage
                                        v-else-if="item.product.photo?.thumbnail_path"
                                        :src="`/storage/${item.product.photo.thumbnail_path}`"
                                        :alt="item.product.name"
                                        class-name="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <Link
                                                :href="route('shop.show', item.product.slug)"
                                                class="text-lg font-light text-gray-900 hover:text-gray-600 tracking-wide transition-colors"
                                            >
                                                {{ item.product.name }}
                                            </Link>
                                            <p class="text-sm text-gray-500 mt-1">{{ item.product.type.toUpperCase() }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeItem(item.id)"
                                            class="text-gray-500 hover:text-gray-900 transition-colors"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="flex justify-between items-end">
                                        <!-- Quantity Controls -->
                                        <div>
                                            <p class="text-xs text-gray-500 mb-2 tracking-wider">QUANTITY</p>
                                            <div class="flex items-center gap-3">
                                                <button
                                                    type="button"
                                                    @click="decrementQuantity(item)"
                                                    class="w-8 h-8 bg-white border border-gray-300 text-gray-900 hover:bg-gray-100 transition-colors flex items-center justify-center"
                                                    :disabled="item.quantity <= 1"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                    </svg>
                                                </button>
                                                <span class="text-gray-900 font-light w-8 text-center">{{ item.quantity }}</span>
                                                <button
                                                    type="button"
                                                    @click="incrementQuantity(item)"
                                                    class="w-8 h-8 bg-white border border-gray-300 text-gray-900 hover:bg-gray-100 transition-colors flex items-center justify-center"
                                                    :disabled="item.quantity >= item.product.stock"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-2">{{ item.product.stock }} available</p>
                                        </div>

                                        <!-- Item Total -->
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500 mb-1">
                                                ${{ parseFloat(item.price).toFixed(2) }} each
                                            </p>
                                            <p class="text-xl font-light text-gray-900">
                                                ${{ (parseFloat(item.price) * item.quantity).toFixed(2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="border border-gray-200 p-6 sticky top-4">
                            <h2 class="text-xl font-light tracking-wide text-gray-900 mb-6">ORDER SUMMARY</h2>

                            <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                                <div class="flex justify-between text-gray-600">
                                    <span class="text-sm tracking-wide">SUBTOTAL</span>
                                    <span class="text-gray-900">${{ parseFloat(total).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span class="text-sm tracking-wide">SHIPPING</span>
                                    <span class="text-gray-900">Calculated at checkout</span>
                                </div>
                            </div>

                            <div class="flex justify-between text-xl font-light text-gray-900 mb-8">
                                <span class="tracking-wide">TOTAL</span>
                                <span>${{ parseFloat(total).toFixed(2) }}</span>
                            </div>

                            <Link
                                :href="route('checkout.index')"
                                class="block w-full bg-black text-white text-center py-4 text-sm font-light tracking-wider hover:bg-gray-800 transition-colors mb-4"
                            >
                                PROCEED TO CHECKOUT
                            </Link>

                            <Link
                                :href="route('shop.index')"
                                class="block w-full text-center text-gray-600 hover:text-gray-900 text-sm tracking-wide transition-colors"
                            >
                                Continue Shopping
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
