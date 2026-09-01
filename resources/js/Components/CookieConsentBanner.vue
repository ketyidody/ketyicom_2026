<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

// Locally hide immediately after a choice for snappy UX; server also stops
// sharing a null cookieConsent on the next request.
const dismissed = ref(false);

const visible = computed(() => !dismissed.value && !page.props.cookieConsent);

const choose = (consent) => {
    router.post(
        route('cookie-consent.store'),
        { consent },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                dismissed.value = true;
            },
        }
    );
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-full opacity-0"
    >
        <div
            v-if="visible"
            class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white/95 backdrop-blur"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <p class="text-sm text-gray-600 leading-relaxed">
                    We use cookies to measure website traffic — such as which pages are visited and
                    which country visitors come from — so we can improve the site. No data is
                    collected until you accept. See our
                    <Link :href="route('about')" class="underline hover:text-black">privacy note</Link>
                    for details.
                </p>
                <div class="flex-shrink-0 flex items-center gap-3">
                    <button
                        type="button"
                        @click="choose('declined')"
                        class="text-sm font-light tracking-wide text-gray-500 hover:text-black transition-colors"
                    >
                        Decline
                    </button>
                    <button
                        type="button"
                        @click="choose('accepted')"
                        class="bg-black text-white text-sm font-light tracking-wide px-5 py-2 hover:bg-gray-800 transition-colors"
                    >
                        Accept
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
