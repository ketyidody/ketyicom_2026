<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    recaptchaSiteKey: String,
});

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    recaptcha_token: '',
});

const isRecaptchaLoaded = ref(false);

const loadRecaptcha = () => {
    if (!props.recaptchaSiteKey) {
        console.error('reCAPTCHA site key is not configured');
        return;
    }

    const script = document.createElement('script');
    script.src = `https://www.google.com/recaptcha/api.js?render=${props.recaptchaSiteKey}`;
    script.addEventListener('load', () => {
        isRecaptchaLoaded.value = true;
    });
    document.head.appendChild(script);
};

const executeRecaptcha = async () => {
    if (!isRecaptchaLoaded.value || !window.grecaptcha) {
        throw new Error('reCAPTCHA not loaded');
    }

    return new Promise((resolve, reject) => {
        window.grecaptcha.ready(() => {
            window.grecaptcha
                .execute(props.recaptchaSiteKey, { action: 'contact' })
                .then(resolve)
                .catch(reject);
        });
    });
};

const submit = async () => {
    try {
        const token = await executeRecaptcha();
        form.recaptcha_token = token;

        form.post(route('contact.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            },
        });
    } catch (error) {
        console.error('reCAPTCHA error:', error);
        alert('Failed to verify reCAPTCHA. Please try again.');
    }
};

onMounted(() => {
    loadRecaptcha();
});
</script>

<template>
    <Head title="Contact" />

    <PublicLayout>
        <!-- Page Header -->
        <div class="border-b border-gray-200 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-light tracking-wide text-gray-900">CONTACT</h1>
            </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-3xl mx-auto">
                <div v-if="$page.props.flash?.success" class="border border-green-800 bg-green-900 bg-opacity-20 p-4 mb-4">
                    <p class="text-sm text-green-400">{{ $page.props.flash.success }}</p>
                </div>
                <div v-if="$page.props.flash?.error" class="border border-red-800 bg-red-900 bg-opacity-20 p-4">
                    <p class="text-sm text-red-400">{{ $page.props.flash.error }}</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-3xl mx-auto">
                <div class="mb-8">
                    <p class="text-gray-600 leading-relaxed">
                        Have a question or want to work together? Fill out the form below and I'll get back to you as soon as possible.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-light tracking-wide text-gray-900 mb-2">
                            NAME <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-500 transition-colors"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-light tracking-wide text-gray-900 mb-2">
                            EMAIL <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-500 transition-colors"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-light tracking-wide text-gray-900 mb-2">
                            SUBJECT
                        </label>
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-500 transition-colors"
                            :class="{ 'border-red-500': form.errors.subject }"
                        />
                        <p v-if="form.errors.subject" class="mt-2 text-sm text-red-600">{{ form.errors.subject }}</p>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-light tracking-wide text-gray-900 mb-2">
                            MESSAGE <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="message"
                            v-model="form.message"
                            required
                            rows="6"
                            class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-500 transition-colors resize-none"
                            :class="{ 'border-red-500': form.errors.message }"
                        ></textarea>
                        <p v-if="form.errors.message" class="mt-2 text-sm text-red-600">{{ form.errors.message }}</p>
                    </div>

                    <!-- reCAPTCHA Badge Notice -->
                    <div class="text-xs text-gray-500">
                        This site is protected by reCAPTCHA and the Google
                        <a href="https://policies.google.com/privacy" target="_blank" class="underline">Privacy Policy</a> and
                        <a href="https://policies.google.com/terms" target="_blank" class="underline">Terms of Service</a> apply.
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing || !isRecaptchaLoaded"
                            class="w-full bg-black text-white py-4 text-sm font-light tracking-wider hover:bg-gray-800 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <span v-if="form.processing">SENDING...</span>
                            <span v-else-if="!isRecaptchaLoaded">LOADING...</span>
                            <span v-else>SEND MESSAGE</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
