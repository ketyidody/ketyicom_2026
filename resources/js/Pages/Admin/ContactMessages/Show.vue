<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    message: Object,
});

const showDeleteModal = ref(false);

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
};

const confirmDelete = () => {
    router.delete(route('admin.contact-messages.destroy', props.message.id));
};

const toggleRead = () => {
    router.patch(route('admin.contact-messages.toggle-read', props.message.id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Contact Message from ${message.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Contact Message</h2>
                <Link :href="route('admin.contact-messages.index')" class="text-sm text-gray-600 hover:text-gray-900">
                    Back to Messages
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Message Header -->
                        <div class="border-b border-gray-200 pb-6 mb-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                                        {{ message.subject || 'No Subject' }}
                                    </h3>
                                    <div class="flex items-center gap-3 text-sm text-gray-600">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                message.is_read ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800'
                                            ]"
                                        >
                                            {{ message.is_read ? 'Read' : 'Unread' }}
                                        </span>
                                        <span>{{ new Date(message.created_at).toLocaleString() }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        @click="toggleRead"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm"
                                    >
                                        Mark as {{ message.is_read ? 'Unread' : 'Read' }}
                                    </button>
                                    <button
                                        @click="openDeleteModal"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sender Information -->
                        <div class="mb-8">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">From</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Name</p>
                                        <p class="text-gray-900 font-medium">{{ message.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Email</p>
                                        <a :href="`mailto:${message.email}`" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ message.email }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Content -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Message</h4>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <p class="text-gray-900 whitespace-pre-line leading-relaxed">{{ message.message }}</p>
                            </div>
                        </div>

                        <!-- Quick Reply -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Quick Actions</h4>
                            <div class="flex gap-3">
                                <a
                                    :href="`mailto:${message.email}?subject=Re: ${message.subject || 'Your Contact Message'}`"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Reply via Email
                                </a>
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
                        <p class="text-gray-600 mb-6">Are you sure you want to delete this message from {{ message.name }}? This action cannot be undone.</p>
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
