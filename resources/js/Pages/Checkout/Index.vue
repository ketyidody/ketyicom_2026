<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    cartItems: Array,
    subtotal: Number,
    shippingCost: Number,
    total: Number,
});

const form = useForm({
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    shipping_address: '',
    city: '',
    postal_code: '',
    country: '',
    notes: '',
});

const submit = () => {
    form.post(route('checkout.store'));
};
</script>

<template>
    <Head title="Checkout" />

    <PublicLayout>
        <!-- Page Header -->
        <div class="border-b border-gray-200 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center">
                    <h1 class="text-4xl md:text-5xl font-light tracking-wide text-gray-900">CHECKOUT</h1>
                    <Link :href="route('cart.index')" class="text-sm text-gray-600 hover:text-gray-900 tracking-wide">
                        BACK TO CART
                    </Link>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.error" class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-7xl mx-auto">
                <div class="border border-red-800 bg-red-900 bg-opacity-20 p-4">
                    <p class="text-sm text-red-400">{{ $page.props.flash.error }}</p>
                </div>
            </div>
        </div>

        <!-- Checkout Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Customer Information -->
                            <div class="border border-gray-200 p-8">
                                <h2 class="text-xl font-light tracking-wide text-gray-900 mb-6">CUSTOMER INFORMATION</h2>

                                <div class="space-y-6">
                                    <div>
                                        <label for="customer_name" class="block text-xs text-gray-500 mb-2 tracking-wider">FULL NAME *</label>
                                        <input
                                            id="customer_name"
                                            v-model="form.customer_name"
                                            type="text"
                                            class="w-full bg-white border border-gray-300 text-gray-900 px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors"
                                            required
                                            autofocus
                                        />
                                        <p v-if="form.errors.customer_name" class="mt-2 text-sm text-red-400">{{ form.errors.customer_name }}</p>
                                    </div>

                                    <div>
                                        <label for="customer_email" class="block text-xs text-gray-500 mb-2 tracking-wider">EMAIL ADDRESS *</label>
                                        <input
                                            id="customer_email"
                                            v-model="form.customer_email"
                                            type="email"
                                            class="w-full bg-white border border-gray-300 text-gray-900 px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors"
                                            required
                                        />
                                        <p v-if="form.errors.customer_email" class="mt-2 text-sm text-red-400">{{ form.errors.customer_email }}</p>
                                    </div>

                                    <div>
                                        <label for="customer_phone" class="block text-xs text-gray-500 mb-2 tracking-wider">PHONE NUMBER</label>
                                        <input
                                            id="customer_phone"
                                            v-model="form.customer_phone"
                                            type="tel"
                                            class="w-full bg-white border border-gray-300 text-gray-900 px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors"
                                        />
                                        <p v-if="form.errors.customer_phone" class="mt-2 text-sm text-red-400">{{ form.errors.customer_phone }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Address -->
                            <div class="border border-gray-200 p-8">
                                <h2 class="text-xl font-light tracking-wide text-gray-900 mb-6">SHIPPING ADDRESS</h2>

                                <div class="space-y-6">
                                    <div>
                                        <label for="shipping_address" class="block text-xs text-gray-500 mb-2 tracking-wider">STREET ADDRESS *</label>
                                        <textarea
                                            id="shipping_address"
                                            v-model="form.shipping_address"
                                            rows="3"
                                            class="w-full bg-white border border-gray-300 text-gray-900 px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors resize-none"
                                            required
                                        ></textarea>
                                        <p v-if="form.errors.shipping_address" class="mt-2 text-sm text-red-400">{{ form.errors.shipping_address }}</p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="city" class="block text-xs text-gray-500 mb-2 tracking-wider">CITY *</label>
                                            <input
                                                id="city"
                                                v-model="form.city"
                                                type="text"
                                                class="w-full bg-white border border-gray-300 text-gray-900 px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors"
                                                required
                                            />
                                            <p v-if="form.errors.city" class="mt-2 text-sm text-red-400">{{ form.errors.city }}</p>
                                        </div>

                                        <div>
                                            <label for="postal_code" class="block text-xs text-gray-500 mb-2 tracking-wider">POSTAL CODE *</label>
                                            <input
                                                id="postal_code"
                                                v-model="form.postal_code"
                                                type="text"
                                                class="w-full bg-white border border-gray-300 text-gray-900 px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors"
                                                required
                                            />
                                            <p v-if="form.errors.postal_code" class="mt-2 text-sm text-red-400">{{ form.errors.postal_code }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="country" class="block text-xs text-gray-500 mb-2 tracking-wider">COUNTRY *</label>
                                        <input
                                            id="country"
                                            v-model="form.country"
                                            type="text"
                                            class="w-full bg-white border border-gray-300 text-gray-900 px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors"
                                            required
                                        />
                                        <p v-if="form.errors.country" class="mt-2 text-sm text-red-400">{{ form.errors.country }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Notes -->
                            <div class="border border-gray-200 p-8">
                                <h2 class="text-xl font-light tracking-wide text-gray-900 mb-6">ORDER NOTES</h2>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    placeholder="Special instructions or notes for your order..."
                                    class="w-full bg-white border border-gray-300 text-gray-900 px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors resize-none placeholder-gray-400"
                                ></textarea>
                                <p v-if="form.errors.notes" class="mt-2 text-sm text-red-400">{{ form.errors.notes }}</p>
                            </div>

                            <!-- Place Order Button -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-black text-white py-4 text-sm font-light tracking-wider hover:bg-gray-800 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <span v-if="form.processing">PROCESSING ORDER...</span>
                                <span v-else>PLACE ORDER</span>
                            </button>
                        </form>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="border border-gray-200 p-6 sticky top-4">
                            <h2 class="text-xl font-light tracking-wide text-gray-900 mb-6">ORDER SUMMARY</h2>

                            <!-- Cart Items -->
                            <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                                <div
                                    v-for="item in cartItems"
                                    :key="item.id"
                                    class="flex justify-between text-sm"
                                >
                                    <div class="flex-1">
                                        <p class="text-gray-900 font-light">{{ item.product.name }}</p>
                                        <p class="text-gray-500 text-xs">Qty: {{ item.quantity }}</p>
                                    </div>
                                    <div class="text-gray-900 font-light ml-4">
                                        ${{ (parseFloat(item.price) * item.quantity).toFixed(2) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                                <div class="flex justify-between text-gray-600">
                                    <span class="text-sm tracking-wide">SUBTOTAL</span>
                                    <span class="text-gray-900">${{ parseFloat(subtotal).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span class="text-sm tracking-wide">SHIPPING</span>
                                    <span class="text-gray-900">${{ parseFloat(shippingCost).toFixed(2) }}</span>
                                </div>
                            </div>

                            <div class="flex justify-between text-xl font-light text-gray-900 mb-6">
                                <span class="tracking-wide">TOTAL</span>
                                <span>${{ parseFloat(total).toFixed(2) }}</span>
                            </div>

                            <!-- Security Notice -->
                            <div class="border border-gray-200 p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gray-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <p class="text-xs text-gray-600 leading-relaxed">
                                        Your information is secure and will only be used to process your order.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
