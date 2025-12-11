<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';

const props = defineProps({
    products: Object,
    types: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const selectedType = ref(props.filters.type || 'all');

const updateFilters = () => {
    router.get(route('shop.index'), {
        search: search.value,
        type: selectedType.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([search, selectedType], () => {
    updateFilters();
}, { debounce: 300 });
</script>

<template>
    <Head title="Shop" />

    <PublicLayout>
        <!-- Shop Header -->
        <div class="border-b border-gray-200 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-light tracking-wide text-gray-900 mb-4">SHOP</h1>
                <p class="text-gray-600 text-lg">Premium prints and photography products</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="border-b border-gray-200 py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search products..."
                            class="w-full bg-white border border-gray-300 text-gray-900 text-sm px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors"
                        />
                    </div>

                    <!-- Type Filter -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 tracking-wide">FILTER:</span>
                        <select
                            v-model="selectedType"
                            class="bg-white border border-gray-300 text-gray-900 text-sm px-4 py-3 focus:outline-none focus:border-gray-400 transition-colors appearance-none cursor-pointer"
                        >
                            <option value="all">ALL TYPES</option>
                            <option v-for="type in types" :key="type" :value="type">
                                {{ type.toUpperCase() }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-7xl mx-auto">
                <div v-if="products.data.length === 0" class="text-center py-24">
                    <svg class="mx-auto h-24 w-24 text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="text-2xl font-light text-gray-700 mb-2">No Products Found</h3>
                    <p class="text-gray-500">Try adjusting your search or filters</p>
                </div>

                <div v-else>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <Link
                            v-for="product in products.data"
                            :key="product.id"
                            :href="route('shop.show', product.slug)"
                            class="group"
                        >
                            <div class="relative overflow-hidden bg-gray-100 aspect-square mb-4">
                                <LazyImage
                                    v-if="product.image"
                                    :src="`/storage/${product.image}`"
                                    :alt="product.name"
                                    class-name="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                />
                                <LazyImage
                                    v-else-if="product.photo?.thumbnail_path"
                                    :src="`/storage/${product.photo.thumbnail_path}`"
                                    :alt="product.name"
                                    class-name="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <svg class="w-20 h-20 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>

                                <!-- Product Type Badge -->
                                <div class="absolute top-3 right-3">
                                    <span class="bg-white text-black px-3 py-1 text-xs font-light tracking-wider">
                                        {{ product.type.toUpperCase() }}
                                    </span>
                                </div>

                                <!-- Out of Stock Overlay -->
                                <div v-if="product.stock === 0" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                                    <span class="text-white font-light text-lg tracking-wider">SOLD OUT</span>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div>
                                <h3 class="text-lg font-light text-gray-900 group-hover:text-gray-600 transition-colors tracking-wide line-clamp-2 mb-2">
                                    {{ product.name }}
                                </h3>
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-light text-gray-900">
                                        ${{ parseFloat(product.price).toFixed(2) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ product.stock }} in stock
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.links.length > 3" class="mt-12 flex justify-center">
                        <nav class="flex gap-2">
                            <Link
                                v-for="(link, index) in products.links"
                                :key="index"
                                :href="link.url"
                                :class="[
                                    'px-4 py-2 text-sm font-light tracking-wide transition-colors',
                                    link.active
                                        ? 'bg-black text-white'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : '',
                                ]"
                                :disabled="!link.url"
                                v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
