<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
});

const form = useForm({
    status: props.order.status,
    notes: props.order.notes,
});

const submit = () => {
    form.put(route('admin.orders.update', props.order.id));
};

const updateStatus = (newStatus) => {
    form.status = newStatus;
    form.patch(route('admin.orders.update-status', props.order.id));
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'EUR',
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order {{ order.order_number }}</h2>
                    <p class="text-sm text-gray-600 mt-1">Placed on {{ formatDate(order.created_at) }}</p>
                </div>
                <Link :href="route('admin.orders.index')" class="text-gray-600 hover:text-gray-900">
                    Back to Orders
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Order Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Order Status</h3>
                        <div class="flex items-center gap-2 mb-4">
                            <span :class="['px-3 py-1 rounded text-sm font-medium', getStatusClass(order.status)]">
                                {{ order.status.toUpperCase() }}
                            </span>
                        </div>

                        <div class="flex gap-2">
                            <button
                                v-if="order.status !== 'pending'"
                                @click="updateStatus('pending')"
                                class="px-4 py-2 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200"
                            >
                                Mark as Pending
                            </button>
                            <button
                                v-if="order.status !== 'processing'"
                                @click="updateStatus('processing')"
                                class="px-4 py-2 text-sm bg-blue-100 text-blue-800 rounded hover:bg-blue-200"
                            >
                                Mark as Processing
                            </button>
                            <button
                                v-if="order.status !== 'completed'"
                                @click="updateStatus('completed')"
                                class="px-4 py-2 text-sm bg-green-100 text-green-800 rounded hover:bg-green-200"
                            >
                                Mark as Completed
                            </button>
                            <button
                                v-if="order.status !== 'cancelled'"
                                @click="updateStatus('cancelled')"
                                class="px-4 py-2 text-sm bg-red-100 text-red-800 rounded hover:bg-red-200"
                            >
                                Mark as Cancelled
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900">{{ order.customer_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ order.customer_email }}</dd>
                                </div>
                                <div v-if="order.customer_phone">
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="text-sm text-gray-900">{{ order.customer_phone }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
                            <address class="text-sm text-gray-900 not-italic">
                                {{ order.shipping_address }}<br>
                                {{ order.city }}, {{ order.postal_code }}<br>
                                {{ order.country }}
                            </address>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Order Items</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantity
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit Price
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in order.items" :key="item.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ item.product_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ item.quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatPrice(item.unit_price) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatPrice(item.total_price) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                                            Subtotal:
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-900">
                                            {{ formatPrice(order.subtotal) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                                            Shipping:
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-900">
                                            {{ formatPrice(order.shipping_cost) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                                            Total:
                                        </td>
                                        <td class="px-6 py-3 text-sm font-semibold text-gray-900">
                                            {{ formatPrice(order.total) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Order Notes</h3>
                        <form @submit.prevent="submit">
                            <textarea
                                v-model="form.notes"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Add notes about this order..."
                            ></textarea>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                            >
                                Save Notes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
