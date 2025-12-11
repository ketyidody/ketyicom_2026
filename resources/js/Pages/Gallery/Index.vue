<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';

const props = defineProps({
    albums: Array,
});
</script>

<template>
    <Head title="Gallery" />

    <PublicLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-7xl mx-auto">
                <!-- Page Title -->
                <div class="text-center mb-16">
                    <h1 class="text-4xl md:text-6xl font-light tracking-wide mb-4">GALLERY</h1>
                    <p class="text-gray-600 text-lg">Explore my photographic work</p>
                </div>

                <!-- Empty State -->
                <div v-if="albums.length === 0" class="text-center py-24">
                    <svg class="mx-auto h-24 w-24 text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-2xl font-light text-gray-700 mb-2">No Albums Yet</h3>
                    <p class="text-gray-500">Check back soon for new collections</p>
                </div>

                <!-- Albums Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <Link
                        v-for="album in albums"
                        :key="album.id"
                        :href="route('gallery.show', album.slug)"
                        class="group"
                    >
                        <div class="relative overflow-hidden bg-gray-100 aspect-[4/3] mb-4">
                            <LazyImage
                                v-if="album.cover_image"
                                :src="`/storage/${album.cover_image}`"
                                :alt="album.title"
                                class-name="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                <svg class="w-20 h-20 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-white bg-opacity-0 group-hover:bg-opacity-70 transition-all duration-300 flex items-center justify-center">
                                <div class="transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 text-center px-6">
                                    <h2 class="text-gray-900 text-2xl font-light tracking-wide mb-2">{{ album.title }}</h2>
                                    <p v-if="album.description" class="text-gray-700 text-sm line-clamp-2 mb-3">{{ album.description }}</p>
                                    <p class="text-gray-600 text-xs tracking-wider">{{ album.photos_count }} {{ album.photos_count === 1 ? 'PHOTO' : 'PHOTOS' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Album Info Below -->
                        <div>
                            <h3 class="text-xl font-light tracking-wide text-gray-900 group-hover:text-gray-600 transition-colors">
                                {{ album.title }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ album.photos_count }} {{ album.photos_count === 1 ? 'photo' : 'photos' }}
                            </p>
                        </div>
                    </Link>
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
