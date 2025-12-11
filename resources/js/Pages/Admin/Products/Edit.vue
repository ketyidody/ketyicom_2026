<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    product: Object,
    photos: Array,
});

const form = useForm({
    photo_id: props.product.photo_id,
    name: props.product.name,
    description: props.product.description,
    type: props.product.type,
    price: props.product.price,
    stock: props.product.stock,
    image: null,
    is_available: props.product.is_available,
});

const imagePreview = ref(null);

const submit = () => {
    form.post(route('admin.products.update', props.product.id), {
        forceFormData: true,
        _method: 'put',
    });
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    form.image = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
    <Head title="Edit Product" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Product: {{ product.name }}</h2>
                <Link :href="route('admin.products.index')" class="text-gray-600 hover:text-gray-900">
                    Back to Products
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <InputLabel for="name" value="Product Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="type" value="Type" />
                                <TextInput
                                    id="type"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.type"
                                    required
                                    placeholder="e.g., print, digital, frame"
                                />
                                <InputError class="mt-2" :message="form.errors.type" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="4"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <InputLabel for="price" value="Price (EUR)" />
                                <TextInput
                                    id="price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    v-model="form.price"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div>
                                <InputLabel for="stock" value="Stock Quantity" />
                                <TextInput
                                    id="stock"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full"
                                    v-model="form.stock"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.stock" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="photo_id" value="Link to Gallery Photo (Optional)" />
                            <select
                                id="photo_id"
                                v-model="form.photo_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">None</option>
                                <option v-for="photo in photos" :key="photo.id" :value="photo.id">
                                    {{ photo.title }} ({{ photo.album.title }})
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.photo_id" />
                            <p class="mt-1 text-sm text-gray-500">Link this product to a photo from your gallery</p>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="image" value="Product Image" />

                            <div v-if="imagePreview" class="mb-4">
                                <img :src="imagePreview" alt="Preview" class="h-32 rounded">
                                <p class="text-sm text-gray-500 mt-1">New preview</p>
                            </div>

                            <div v-else-if="product.image" class="mb-4">
                                <img :src="`/storage/${product.image}`" :alt="product.name" class="h-32 rounded">
                                <p class="text-sm text-gray-500 mt-1">Current image</p>
                            </div>

                            <input
                                id="image"
                                type="file"
                                accept="image/*"
                                @change="handleFileChange"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            />
                            <InputError class="mt-2" :message="form.errors.image" />
                            <p class="mt-1 text-sm text-gray-500">Leave empty to keep current image</p>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_available"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Available for purchase</span>
                            </label>
                            <InputError class="mt-2" :message="form.errors.is_available" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Update Product
                            </PrimaryButton>

                            <Link :href="route('admin.products.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </Link>

                            <div v-if="form.processing" class="text-sm text-gray-600">
                                Updating...
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
