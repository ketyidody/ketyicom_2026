<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';

const props = defineProps({
    product: Object,
    isInCart: Boolean,
});

const quantity = ref(1);
const productInCart = ref(props.isInCart);

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
});

const addToCart = () => {
    form.quantity = quantity.value;
    form.post(route('cart.store'), {
        preserveScroll: true,
        onSuccess: () => {
            quantity.value = 1;
            productInCart.value = true;
        },
    });
};

const incrementQuantity = () => {
    if (quantity.value < props.product.stock) {
        quantity.value++;
    }
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};
</script>

<template>
    <Head :title="product.name" />

    <PublicLayout>
        <!-- Product Details -->
        <div class="px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-7xl mx-auto">
                <!-- Back Link -->
                <Link :href="route('shop.index')" class="text-sm text-gray-600 hover:text-gray-900 mb-8 inline-flex items-center tracking-wide">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    BACK TO SHOP
                </Link>

                <!-- Product Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mt-8">
                    <!-- Product Image -->
                    <div>
                        <div class="relative overflow-hidden bg-gray-100 aspect-square">
                            <LazyImage
                                v-if="product.image"
                                :src="`/storage/${product.image}`"
                                :alt="product.name"
                                class-name="w-full h-full object-cover"
                            />
                            <LazyImage
                                v-else-if="product.photo?.image_path"
                                :src="`/storage/${product.photo.image_path}`"
                                :alt="product.name"
                                class-name="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                <svg class="w-32 h-32 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Related Photo Link -->
                        <div v-if="product.photo" class="mt-6 p-4 border border-gray-200">
                            <p class="text-xs text-gray-500 mb-2 tracking-wider">FROM THE COLLECTION</p>
                            <Link
                                v-if="product.photo.album"
                                :href="route('gallery.show', product.photo.album.slug)"
                                class="text-gray-900 hover:text-gray-600 font-light tracking-wide transition-colors"
                            >
                                {{ product.photo.album.title }}
                            </Link>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-8">
                        <!-- Product Type -->
                        <div>
                            <span class="inline-block bg-white text-black px-4 py-1 text-xs font-light tracking-wider">
                                {{ product.type.toUpperCase() }}
                            </span>
                        </div>

                        <!-- Product Name & Price -->
                        <div>
                            <h1 class="text-4xl md:text-5xl font-light tracking-wide text-gray-900 mb-6">
                                {{ product.name }}
                            </h1>
                            <p class="text-3xl font-light text-gray-900">
                                ${{ parseFloat(product.price).toFixed(2) }}
                            </p>
                        </div>

                        <!-- Product Description -->
                        <div v-if="product.description" class="border-t border-b border-gray-200 py-6">
                            <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
                        </div>

                        <!-- Stock Info -->
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-500 tracking-wide">AVAILABILITY:</span>
                            <span v-if="product.stock > 0" class="text-sm text-gray-900">
                                {{ product.stock }} IN STOCK
                            </span>
                            <span v-else class="text-sm text-red-400">OUT OF STOCK</span>
                        </div>

                        <!-- Add to Cart Form -->
                        <div v-if="product.stock > 0" class="space-y-6 pt-4">
                            <!-- Quantity Selector -->
                            <div>
                                <label class="block text-sm text-gray-500 mb-3 tracking-wider">QUANTITY</label>
                                <div class="flex items-center gap-4">
                                    <button
                                        type="button"
                                        @click="decrementQuantity"
                                        class="w-12 h-12 bg-white border border-gray-300 text-gray-900 hover:bg-gray-100 transition-colors flex items-center justify-center"
                                        :disabled="quantity <= 1"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input
                                        v-model.number="quantity"
                                        type="number"
                                        min="1"
                                        :max="product.stock"
                                        class="w-20 text-center bg-white border border-gray-300 text-gray-900 py-3 focus:outline-none focus:border-gray-400"
                                    />
                                    <button
                                        type="button"
                                        @click="incrementQuantity"
                                        class="w-12 h-12 bg-white border border-gray-300 text-gray-900 hover:bg-gray-100 transition-colors flex items-center justify-center"
                                        :disabled="quantity >= product.stock"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <button
                                type="button"
                                @click="addToCart"
                                :disabled="form.processing || productInCart"
                                class="w-full bg-black text-white py-4 text-sm font-light tracking-wider hover:bg-gray-800 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <span v-if="form.processing">ADDING TO CART...</span>
                                <span v-else-if="productInCart">ALREADY IN CART</span>
                                <span v-else>ADD TO CART</span>
                            </button>

                            <!-- Flash Messages -->
                            <div v-if="$page.props.flash?.success" class="border border-green-800 bg-green-900 bg-opacity-20 p-4">
                                <p class="text-sm text-green-400">{{ $page.props.flash.success }}</p>
                            </div>

                            <div v-if="$page.props.flash?.error" class="border border-red-800 bg-red-900 bg-opacity-20 p-4">
                                <p class="text-sm text-red-400">{{ $page.props.flash.error }}</p>
                            </div>
                        </div>

                        <!-- Out of Stock Message -->
                        <div v-else class="border border-red-800 bg-red-900 bg-opacity-20 p-6">
                            <p class="text-sm text-red-400 tracking-wide">THIS PRODUCT IS CURRENTLY OUT OF STOCK</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
