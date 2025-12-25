<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    messages: Object,
    filters: Object,
});

const searchForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || 'all',
});

const showDeleteModal = ref(false);
const messageToDelete = ref(null);

const search = () => {
    searchForm.get(route('admin.contact-messages.index'), {
        preserveState: true,
        replace: true,
    });
};

const openDeleteModal = (message) => {
    messageToDelete.value = message;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    messageToDelete.value = null;
};

const confirmDelete = () => {
    if (messageToDelete.value) {
        router.delete(route('admin.contact-messages.destroy', messageToDelete.value.id), {
            onFinish: () => {
                closeDeleteModal();
            },
        });
    }
};

const toggleRead = (message) => {
    router.patch(route('admin.contact-messages.toggle-read', message.id), {}, {
        preserveScroll: true,
    });
};

watch(() => searchForm.search, () => {
    if (searchForm.search === '') {
        search();
    }
});

watch(() => searchForm.status, () => {
    search();
});
</script>

<template>
    <Head title="Contact Messages" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Contact Messages</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Filters -->
                        <div class="mb-6 flex flex-col sm:flex-row gap-4">
                            <input
                                v-model="searchForm.search"
                                @input="search"
                                type="text"
                                placeholder="Search by name, email, or subject..."
                                class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            />
                            <select
                                v-model="searchForm.status"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="all">All Messages</option>
                                <option value="unread">Unread</option>
                                <option value="read">Read</option>
                            </select>
                        </div>

                        <!-- Flash Messages -->
                        <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div v-if="messages.data.length === 0" class="text-center py-8 text-gray-500">
                            No contact messages found.
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            From
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subject
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Message Preview
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Received
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="message in messages.data" :key="message.id" class="hover:bg-gray-50" :class="{ 'bg-blue-50': !message.is_read }">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button
                                                @click="toggleRead(message)"
                                                :class="[
                                                    'w-3 h-3 rounded-full cursor-pointer',
                                                    message.is_read ? 'bg-gray-300 hover:bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'
                                                ]"
                                                :title="message.is_read ? 'Mark as unread' : 'Mark as read'"
                                            ></button>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ message.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ message.email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                                {{ message.subject || 'No subject' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-600 max-w-md truncate">
                                                {{ message.message }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(message.created_at).toLocaleString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <Link :href="route('admin.contact-messages.show', message.id)" class="text-blue-600 hover:text-blue-900">
                                                    View
                                                </Link>
                                                <button @click="openDeleteModal(message)" class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div v-if="messages.links && messages.links.length > 3" class="mt-6 flex justify-center">
                                <nav class="flex gap-2">
                                    <template v-for="(link, index) in messages.links" :key="index">
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

        <!-- Delete Confirmation Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                @click.self="closeDeleteModal"
            >
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="showDeleteModal"
                        class="bg-white max-w-md w-full p-6 rounded-lg shadow-xl"
                    >
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Contact Message</h3>
                        <p class="text-gray-600 mb-6">Are you sure you want to delete this message? This action cannot be undone.</p>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                @click="closeDeleteModal"
                                class="flex-1 bg-white border border-gray-300 text-gray-900 py-2 px-4 rounded-md hover:bg-gray-100"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                @click="confirmDelete"
                                class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>
