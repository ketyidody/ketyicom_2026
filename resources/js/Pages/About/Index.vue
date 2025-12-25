<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';

const props = defineProps({
    aboutImage: String,
    aboutText: String,
});
</script>

<template>
    <Head title="About" />

    <PublicLayout>
        <!-- Page Header -->
        <div class="border-b border-gray-200 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-light tracking-wide text-gray-900">WHO AM I</h1>
            </div>
        </div>

        <!-- About Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-5xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                    <!-- About Image -->
                    <div v-if="aboutImage" class="order-1 lg:order-1">
                        <div class="relative overflow-hidden bg-gray-100 aspect-[3/4]">
                            <LazyImage
                                :src="`/storage/${aboutImage}`"
                                alt="About me"
                                class-name="w-full h-full object-cover"
                            />
                        </div>
                    </div>

                    <!-- Placeholder Image if no image is set -->
                    <div v-else class="order-1 lg:order-1">
                        <div class="relative overflow-hidden bg-gray-100 aspect-[3/4] flex items-center justify-center">
                            <svg class="w-32 h-32 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <!-- About Text -->
                    <div class="order-2 lg:order-2 space-y-6">
                        <div v-if="aboutText" class="prose prose-lg max-w-none">
                            <div class="text-gray-600 leading-relaxed whitespace-pre-line" v-html="aboutText"></div>
                        </div>
                        <div v-else class="text-gray-500 italic">
                            <p>No introduction text has been added yet.</p>
                            <p class="mt-4 text-sm">This content can be managed from the admin settings panel.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
