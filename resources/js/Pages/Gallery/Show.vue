<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';
import Lightbox from '@/Components/Gallery/Lightbox.vue';

const props = defineProps({
    album: Object,
    photos: Array,
});

const showLightbox = ref(false);
const currentPhotoIndex = ref(0);

const openLightbox = (index) => {
    currentPhotoIndex.value = index;
    showLightbox.value = true;
};

const closeLightbox = () => {
    showLightbox.value = false;
};

const navigateToPhoto = (index) => {
    currentPhotoIndex.value = index;
};
</script>

<template>
    <Head :title="album.title" />

    <PublicLayout>
        <!-- Album Header -->
        <div class="border-b border-gray-200 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <Link :href="route('gallery.index')" class="text-sm text-gray-600 hover:text-gray-900 mb-4 inline-flex items-center tracking-wide">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    BACK TO GALLERY
                </Link>
                <h1 class="text-4xl md:text-5xl font-light tracking-wide text-gray-900 mt-4 mb-3">{{ album.title }}</h1>
                <p v-if="album.description" class="text-gray-600 text-lg max-w-3xl">{{ album.description }}</p>
                <p class="text-gray-500 text-sm mt-4 tracking-wide">{{ photos.length }} {{ photos.length === 1 ? 'PHOTO' : 'PHOTOS' }}</p>
            </div>
        </div>

        <!-- Photos Masonry Grid -->
        <div class="px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-7xl mx-auto">
                <div v-if="photos.length === 0" class="text-center py-24">
                    <svg class="mx-auto h-24 w-24 text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-light text-gray-700">No photos in this album yet</h3>
                </div>

                <!-- Masonry Layout -->
                <div v-else class="columns-1 md:columns-2 lg:columns-3 gap-4 space-y-4">
                    <div
                        v-for="(photo, index) in photos"
                        :key="photo.id"
                        @click="openLightbox(index)"
                        class="group cursor-pointer break-inside-avoid"
                    >
                        <div class="relative overflow-hidden bg-gray-100">
                            <LazyImage
                                :src="photo.medium_path ? `/storage/${photo.medium_path}` : `/storage/${photo.image_path}`"
                                :alt="photo.title"
                                class-name="w-full h-auto transition-all duration-500 group-hover:scale-105"
                            />

                            <!-- Overlay on Hover -->
                            <div class="absolute inset-0 bg-white bg-opacity-0 group-hover:bg-opacity-70 transition-opacity duration-300 flex items-center justify-center p-6">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-gray-900 text-center">
                                    <h3 class="font-light text-lg tracking-wide mb-2">{{ photo.title }}</h3>
                                    <p v-if="photo.description" class="text-sm text-gray-700 line-clamp-3 mb-3">{{ photo.description }}</p>
                                    <p class="text-xs text-gray-600 tracking-wider">CLICK TO VIEW</p>
                                </div>
                            </div>

                            <!-- Featured Badge -->
                            <div v-if="photo.is_featured" class="absolute top-3 right-3">
                                <span class="bg-white text-black px-3 py-1 text-xs font-light tracking-wider">
                                    FEATURED
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox -->
        <Lightbox
            :photos="photos"
            :current-index="currentPhotoIndex"
            :show="showLightbox"
            @close="closeLightbox"
            @navigate="navigateToPhoto"
        />
    </PublicLayout>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
