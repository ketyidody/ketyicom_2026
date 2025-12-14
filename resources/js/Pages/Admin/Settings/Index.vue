<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    settings: Object,
    filters: Object,
});

const searchForm = useForm({
    search: props.filters.search || '',
});

const deleteSetting = (setting) => {
    if (confirm(`Are you sure you want to delete setting "${setting.key}"?`)) {
        router.delete(route('admin.settings.destroy', setting.id));
    }
};

const search = () => {
    searchForm.get(route('admin.settings.index'), {
        preserveState: true,
        replace: true,
    });
};

watch(() => searchForm.search, () => {
    if (searchForm.search === '') {
        search();
    }
});
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings</h2>
                <Link :href="route('admin.settings.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Setting
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Search Bar -->
                        <div class="mb-6">
                            <form @submit.prevent="search" class="flex gap-2">
                                <input
                                    v-model="searchForm.search"
                                    type="text"
                                    placeholder="Search by key or description..."
                                    class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                />
                                <button
                                    type="submit"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md"
                                >
                                    Search
                                </button>
                            </form>
                        </div>

                        <div v-if="settings.data.length === 0" class="text-center py-8 text-gray-500">
                            No settings found. Create your first setting!
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Key
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Updated
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="setting in settings.data" :key="setting.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ setting.key }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-600 max-w-md truncate">
                                                {{ setting.description || 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                setting.active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                            ]">
                                                {{ setting.active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(setting.updated_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <Link :href="route('admin.settings.edit', setting.id)" class="text-blue-600 hover:text-blue-900">
                                                    Edit
                                                </Link>
                                                <button @click="deleteSetting(setting)" class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div v-if="settings.links && settings.links.length > 3" class="mt-6 flex justify-center">
                                <nav class="flex gap-2">
                                    <template v-for="(link, index) in settings.links" :key="index">
                                        <Link
                                            v-if="link.url"
                                            :href="link.url"
                                            :class="[
                                                'px-3 py-2 rounded-md text-sm',
                                                link.active
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                            ]"
                                            v-html="link.label"
                                        />
                                        <span
                                            v-else
                                            :class="[
                                                'px-3 py-2 rounded-md text-sm',
                                                'bg-gray-100 text-gray-400 border border-gray-200'
                                            ]"
                                            v-html="link.label"
                                        />
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
