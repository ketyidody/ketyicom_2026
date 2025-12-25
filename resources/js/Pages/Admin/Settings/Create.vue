<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const form = useForm({
    key: '',
    text: '',
    description: '',
    active: true,
    image: null,
    content_type: 'text', // 'text' or 'image'
});

const imagePreview = ref(null);

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
};

const submit = () => {
    // Always use POST for create
    form.post(route('admin.settings.store'));
};
</script>

<template>
    <Head title="Create Setting" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Setting</h2>
                <Link :href="route('admin.settings.index')" class="text-gray-600 hover:text-gray-900">
                    Back to Settings
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <InputLabel for="key" value="Key" />
                            <TextInput
                                id="key"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.key"
                                required
                                autofocus
                                placeholder="e.g., homepage.announcement"
                            />
                            <InputError class="mt-2" :message="form.errors.key" />
                            <p class="mt-1 text-sm text-gray-500">Unique identifier for this setting (use lowercase, dots, underscores)</p>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="2"
                                placeholder="Where is this setting used?"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                            <p class="mt-1 text-sm text-gray-500">Describe where this snippet is used (e.g., "Homepage announcement banner")</p>
                        </div>

                        <!-- Content Type Selector -->
                        <div class="mb-4">
                            <InputLabel value="Content Type" />
                            <div class="mt-2 flex gap-4">
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.content_type"
                                        value="text"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Text/HTML</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.content_type"
                                        value="image"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Image</span>
                                </label>
                            </div>
                        </div>

                        <!-- Text/HTML Content -->
                        <div v-if="form.content_type === 'text'" class="mb-4">
                            <InputLabel for="text" value="HTML Content" />
                            <textarea
                                id="text"
                                v-model="form.text"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono text-sm"
                                rows="10"
                                placeholder="<div>Your HTML content here...</div>"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.text" />
                            <p class="mt-1 text-sm text-gray-500">Raw HTML content (will be rendered as-is on the website)</p>
                        </div>

                        <!-- Image Upload -->
                        <div v-if="form.content_type === 'image'" class="mb-4">
                            <InputLabel for="image" value="Image Upload" />
                            <div class="mt-2">
                                <input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.image" />
                            <p class="mt-1 text-sm text-gray-500">Upload an image file (max 10MB). The path will be saved automatically.</p>

                            <!-- Image Preview -->
                            <div v-if="imagePreview" class="mt-4">
                                <p class="text-sm text-gray-700 mb-2">Preview:</p>
                                <div class="relative inline-block">
                                    <img :src="imagePreview" alt="Preview" class="max-w-xs max-h-64 rounded border border-gray-300" />
                                    <button
                                        type="button"
                                        @click="removeImage"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.active"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                            <InputError class="mt-2" :message="form.errors.active" />
                            <p class="mt-1 text-sm text-gray-500">Only active settings will be displayed on the website</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Create Setting
                            </PrimaryButton>

                            <Link :href="route('admin.settings.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </Link>

                            <div v-if="form.processing" class="text-sm text-gray-600">
                                Creating...
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
