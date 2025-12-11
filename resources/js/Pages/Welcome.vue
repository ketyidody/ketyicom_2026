<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';

defineProps({
    featuredPhotos: Array,
    albums: Array,
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Home" />

    <PublicLayout>
        <!-- Hero Section with Featured Photos -->
        <div class="relative">
            <!-- Admin Login Link (top right, subtle) -->
            <div v-if="canLogin" class="absolute top-4 right-4 z-10">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-xs text-gray-600 hover:text-gray-900 transition-colors tracking-wide"
                >
                    ADMIN
                </Link>
                <Link
                    v-else
                    :href="route('login')"
                    class="text-xs text-gray-600 hover:text-gray-900 transition-colors tracking-wide"
                >
                    LOGIN
                </Link>
            </div>

            <!-- Featured Photos Masonry Grid -->
            <div v-if="featuredPhotos.length > 0" class="px-4 sm:px-6 lg:px-8 py-12">
                <div class="max-w-7xl mx-auto">
                    <div class="columns-1 md:columns-2 lg:columns-3 gap-4 space-y-4">
                        <Link
                            v-for="photo in featuredPhotos"
                            :key="photo.id"
                            :href="photo.album ? route('gallery.show', photo.album.slug) : '#'"
                            class="block break-inside-avoid group"
                        >
                            <div class="relative overflow-hidden bg-gray-100">
                                <LazyImage
                                    v-if="photo.medium_path"
                                    :src="`/storage/${photo.medium_path}`"
                                    :alt="photo.title"
                                    class-name="w-full h-auto transition-transform duration-500 group-hover:scale-105"
                                />
                                <!-- Overlay on hover -->
                                <div class="absolute inset-0 bg-white bg-opacity-0 group-hover:bg-opacity-70 transition-opacity duration-300 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center p-4">
                                        <h3 class="text-gray-900 text-lg font-light tracking-wide">{{ photo.title }}</h3>
                                        <p v-if="photo.album" class="text-gray-700 text-sm mt-1">{{ photo.album.title }}</p>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="px-4 sm:px-6 lg:px-8 py-24">
                <div class="max-w-3xl mx-auto text-center">
                    <h1 class="text-4xl md:text-6xl font-light tracking-wide mb-6">COMING SOON</h1>
                    <p class="text-gray-600 text-lg mb-8">New work will be showcased here.</p>
                    <Link
                        :href="route('gallery.index')"
                        class="inline-block bg-black text-white px-8 py-3 text-sm font-light tracking-wide hover:bg-gray-800 transition-colors"
                    >
                        EXPLORE GALLERY
                    </Link>
                </div>
            </div>
        </div>

        <!-- Albums Section -->
        <div v-if="albums.length > 0" class="px-4 sm:px-6 lg:px-8 py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-light tracking-wide mb-12 text-center">COLLECTIONS</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <Link
                        v-for="album in albums"
                        :key="album.id"
                        :href="route('gallery.show', album.slug)"
                        class="group"
                    >
                        <div class="relative overflow-hidden bg-gray-100 aspect-[4/3]">
                            <LazyImage
                                v-if="album.cover_image"
                                :src="`/storage/${album.cover_image}`"
                                :alt="album.title"
                                class-name="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <!-- Album Info Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-0 left-0 right-0 p-6">
                                    <h3 class="text-xl font-light tracking-wide text-gray-900 mb-1">{{ album.title }}</h3>
                                    <p class="text-gray-700 text-sm">{{ album.photos_count }} {{ album.photos_count === 1 ? 'Photo' : 'Photos' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-light tracking-wide text-gray-900 group-hover:text-gray-600 transition-colors">{{ album.title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ album.photos_count }} {{ album.photos_count === 1 ? 'photo' : 'photos' }}</p>
                        </div>
                    </Link>
                </div>

                <div class="text-center mt-12">
                    <Link
                        :href="route('gallery.index')"
                        class="inline-block border border-white text-gray-900 px-8 py-3 text-sm font-light tracking-wide hover:bg-white hover:text-black transition-colors"
                    >
                        VIEW ALL ALBUMS
                    </Link>
                </div>
            </div>
        </div>

        <!-- Shop CTA -->
        <div class="px-4 sm:px-6 lg:px-8 py-24 bg-black">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-4xl font-light tracking-wide mb-6">SHOP PRINTS</h2>
                <p class="text-gray-600 text-lg mb-8">High-quality prints available for your collection</p>
                <Link
                    :href="route('shop.index')"
                    class="inline-block bg-black text-white px-8 py-3 text-sm font-light tracking-wide hover:bg-gray-800 transition-colors"
                >
                    BROWSE SHOP
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>
