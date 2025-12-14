<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    key: '',
    text: '',
    description: '',
    active: true,
});

const submit = () => {
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

                        <div class="mb-4">
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
