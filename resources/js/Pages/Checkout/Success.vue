<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    order: Object,
});
</script>

<template>
    <Head title="Order Confirmed" />

    <PublicLayout>
        <!-- Success Header -->
        <div class="border-b border-gray-200 py-16 px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-3xl mx-auto">
                <div class="mb-6">
                    <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-light tracking-wide text-gray-900 mb-4">ORDER CONFIRMED</h1>
                <p class="text-gray-600 text-lg">Thank you for your purchase</p>
            </div>
        </div>

        <!-- Order Details -->
        <div class="px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-4xl mx-auto space-y-8">
                <!-- Order Information -->
                <div class="border border-gray-200 p-8">
                    <h2 class="text-xl font-light tracking-wide text-gray-900 mb-6">ORDER DETAILS</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 mb-1 tracking-wider">ORDER NUMBER</p>
                            <p class="text-gray-900 font-light">{{ order.order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1 tracking-wider">ORDER DATE</p>
                            <p class="text-gray-900 font-light">{{ new Date(order.created_at).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1 tracking-wider">STATUS</p>
                            <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 text-xs font-light tracking-wider">
                                {{ order.status.toUpperCase() }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1 tracking-wider">CONFIRMATION EMAIL</p>
                            <p class="text-gray-900 font-light">{{ order.customer_email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Notice -->
                <div class="border border-blue-200 bg-blue-50 p-6">
                    <p class="text-sm text-blue-700">
                        A confirmation email has been sent to <span class="font-medium">{{ order.customer_email }}</span> with your order details.
                    </p>
                </div>

                <!-- Customer Information -->
                <div class="border border-gray-200 p-8">
                    <h2 class="text-xl font-light tracking-wide text-gray-900 mb-6">CUSTOMER INFORMATION</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <p class="text-xs text-gray-500 mb-3 tracking-wider">CONTACT DETAILS</p>
                            <div class="space-y-1">
                                <p class="text-gray-900">{{ order.customer_name }}</p>
                                <p class="text-gray-600">{{ order.customer_email }}</p>
                                <p v-if="order.customer_phone" class="text-gray-600">{{ order.customer_phone }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-3 tracking-wider">SHIPPING ADDRESS</p>
                            <div class="space-y-1">
                                <p class="text-gray-900">{{ order.shipping_address }}</p>
                                <p class="text-gray-600">{{ order.city }}, {{ order.postal_code }}</p>
                                <p class="text-gray-600">{{ order.country }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="order.notes" class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-xs text-gray-500 mb-2 tracking-wider">ORDER NOTES</p>
                        <p class="text-gray-600">{{ order.notes }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="border border-gray-200 p-8">
                    <h2 class="text-xl font-light tracking-wide text-gray-900 mb-6">ORDER ITEMS</h2>
                    <div class="space-y-4">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex justify-between items-center py-4 border-b border-gray-200 last:border-b-0"
                        >
                            <div class="flex-1">
                                <p class="font-light text-gray-900">{{ item.product_name }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Quantity: {{ item.quantity }} × ${{ parseFloat(item.unit_price).toFixed(2) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-light text-gray-900">
                                    ${{ parseFloat(item.total_price).toFixed(2) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Totals -->
                    <div class="mt-8 pt-6 border-t border-gray-200 space-y-3">
                        <div class="flex justify-between text-gray-600">
                            <span class="tracking-wide">SUBTOTAL</span>
                            <span class="text-gray-900">${{ parseFloat(order.subtotal).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span class="tracking-wide">SHIPPING</span>
                            <span class="text-gray-900">${{ parseFloat(order.shipping_cost).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-light text-gray-900 pt-3">
                            <span class="tracking-wide">TOTAL</span>
                            <span>${{ parseFloat(order.total).toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8">
                    <Link
                        :href="route('shop.index')"
                        class="inline-block bg-black text-white text-center px-8 py-4 text-sm font-light tracking-wider hover:bg-gray-800 transition-colors"
                    >
                        CONTINUE SHOPPING
                    </Link>
                    <Link
                        :href="route('gallery.index')"
                        class="inline-block border border-gray-200 text-gray-900 text-center px-8 py-4 text-sm font-light tracking-wider hover:bg-gray-100 transition-colors"
                    >
                        BROWSE GALLERY
                    </Link>
                </div>

                <!-- Support Information -->
                <div class="text-center pt-8">
                    <p class="text-sm text-gray-500">
                        Questions about your order? Contact us at
                        <a href="mailto:support@example.com" class="text-gray-900 hover:text-gray-600 transition-colors">
                            support@example.com
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
