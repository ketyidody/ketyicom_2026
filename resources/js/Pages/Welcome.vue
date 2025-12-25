<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';
import Lightbox from '@/Components/Gallery/Lightbox.vue';

const props = defineProps({
    featuredPhotos: Array,
    albums: Array,
    canLogin: Boolean,
    canRegister: Boolean,
});

const carouselRef = ref(null);
const showLeftArrow = ref(false);
const showRightArrow = ref(true);
const autoScrollInterval = ref(null);
const isHovering = ref(false);

// Lightbox state
const showLightbox = ref(false);
const currentPhotoIndex = ref(0);

// Duplicate photos for infinite scroll effect
const infinitePhotos = computed(() => {
    if (!props.featuredPhotos || props.featuredPhotos.length === 0) return [];
    // Triple the array for seamless infinite scroll
    return [...props.featuredPhotos, ...props.featuredPhotos, ...props.featuredPhotos];
});

const scroll = (direction) => {
    if (!carouselRef.value) return;

    const scrollAmount = carouselRef.value.offsetWidth * 0.5;
    const newScrollLeft = carouselRef.value.scrollLeft + (direction === 'right' ? scrollAmount : -scrollAmount);

    carouselRef.value.scrollTo({
        left: newScrollLeft,
        behavior: 'smooth'
    });
};

const updateArrows = () => {
    if (!carouselRef.value) return;

    const { scrollLeft, scrollWidth, clientWidth } = carouselRef.value;
    showLeftArrow.value = scrollLeft > 0;
    showRightArrow.value = scrollLeft < scrollWidth - clientWidth - 10;
};

const autoScroll = () => {
    if (!carouselRef.value || isHovering.value) return;

    const scrollSpeed = 1; // pixels per frame
    const currentScroll = carouselRef.value.scrollLeft;
    const maxScroll = carouselRef.value.scrollWidth - carouselRef.value.clientWidth;
    const sectionWidth = carouselRef.value.scrollWidth / 3;

    // Smooth continuous scroll
    carouselRef.value.scrollLeft += scrollSpeed;

    // Reset to middle section when reaching end of second section
    if (currentScroll >= sectionWidth * 2) {
        carouselRef.value.scrollLeft = sectionWidth;
    }
};

const startAutoScroll = () => {
    if (autoScrollInterval.value) return;
    autoScrollInterval.value = setInterval(autoScroll, 30); // ~33fps
};

const stopAutoScroll = () => {
    if (autoScrollInterval.value) {
        clearInterval(autoScrollInterval.value);
        autoScrollInterval.value = null;
    }
};

const handleMouseEnter = () => {
    isHovering.value = true;
};

const handleMouseLeave = () => {
    isHovering.value = false;
};

// Lightbox functions
const openLightbox = (index) => {
    // Map the carousel index to the actual photo index (handling infinite scroll)
    const actualIndex = index % props.featuredPhotos.length;
    currentPhotoIndex.value = actualIndex;
    showLightbox.value = true;
};

const closeLightbox = () => {
    showLightbox.value = false;
};

const navigateToPhoto = (index) => {
    currentPhotoIndex.value = index;
};

onMounted(() => {
    if (carouselRef.value && props.featuredPhotos?.length > 0) {
        // Start at the middle section for seamless loop
        const sectionWidth = carouselRef.value.scrollWidth / 3;
        carouselRef.value.scrollLeft = sectionWidth;

        // Start auto-scroll after a brief delay
        setTimeout(() => {
            startAutoScroll();
        }, 500);
    }
});

onUnmounted(() => {
    stopAutoScroll();
});
</script>

<template>
    <Head title="Home" />

    <PublicLayout>
        <!-- Hero Section with Featured Photos -->
        <div class="relative">
            <!-- Featured Photos Carousel -->
            <div v-if="featuredPhotos.length > 0" class="py-12 relative">
                <div class="max-w-full">
                    <!-- Carousel Container -->
                    <div
                        ref="carouselRef"
                        @scroll="updateArrows"
                        @mouseenter="handleMouseEnter"
                        @mouseleave="handleMouseLeave"
                        class="flex gap-4 overflow-x-auto scrollbar-hide px-4 sm:px-6 lg:px-8"
                        style="scroll-behavior: auto;"
                    >
                        <div
                            v-for="(photo, index) in infinitePhotos"
                            :key="`photo-${index}`"
                            @click="openLightbox(index)"
                            class="flex-none w-[60vw] sm:w-[49vw] md:w-[42vw] lg:w-[35vw] xl:w-[28vw] group cursor-pointer"
                        >
                            <div class="relative overflow-hidden bg-gray-100 h-[49vh] max-h-[420px]">
                                <LazyImage
                                    v-if="photo.medium_path"
                                    :src="`/storage/${photo.medium_path}`"
                                    :alt="photo.title"
                                    class-name="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <!-- Overlay on hover -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center p-4">
                                        <h3 class="text-white text-xl md:text-2xl font-light tracking-wide">{{ photo.title }}</h3>
                                        <p v-if="photo.album" class="text-gray-200 text-sm md:text-base mt-2">{{ photo.album.title }}</p>
                                        <p class="text-gray-300 text-xs mt-3 tracking-wider">CLICK TO VIEW</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Arrows -->
                    <button
                        v-if="showLeftArrow"
                        @click="scroll('left')"
                        @mouseenter="handleMouseEnter"
                        @mouseleave="handleMouseLeave"
                        class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-900 w-12 h-12 items-center justify-center rounded-full shadow-lg transition-all duration-200 hover:scale-110"
                        aria-label="Previous"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        v-if="showRightArrow"
                        @click="scroll('right')"
                        @mouseenter="handleMouseEnter"
                        @mouseleave="handleMouseLeave"
                        class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-900 w-12 h-12 items-center justify-center rounded-full shadow-lg transition-all duration-200 hover:scale-110"
                        aria-label="Next"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
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
                        class="inline-block border border-gray-500 text-gray-900 px-8 py-3 text-sm font-light tracking-wide hover:bg-gray-100 hover:text-black transition-colors"
                    >
                        VIEW ALL ALBUMS
                    </Link>
                </div>
            </div>
        </div>

        <!-- Shop CTA -->
        <div class="px-4 sm:px-6 lg:px-8 py-24 bg-black" v-if="$page.props.shopctaTitle && $page.props.shopctaDesc">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-4xl font-light text-gray-500 tracking-wide mb-6" v-html="$page.props.shopctaTitle"></h2>
                <div class="text-gray-600 text-lg mb-8" v-html="$page.props.shopctaDesc"></div>
                <Link
                    :href="route('shop.index')"
                    class="inline-block bg-black text-white px-8 py-3 text-sm font-light tracking-wide hover:bg-gray-800 transition-colors"
                >
                    BROWSE SHOP
                </Link>
            </div>
        </div>

        <!-- Lightbox -->
        <Lightbox
            :photos="featuredPhotos"
            :current-index="currentPhotoIndex"
            :show="showLightbox"
            @close="closeLightbox"
            @navigate="navigateToPhoto"
        />
    </PublicLayout>
</template>
