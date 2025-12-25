<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const mobileMenuOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900">
        <!-- Announcement Banner -->
        <div
            v-if="$page.props.announcements"
            class="bg-black text-white text-center py-2 px-4 text-sm"
            v-html="$page.props.announcements"
        >
        </div>

        <!-- Navigation -->
        <nav class="border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo / Brand -->
                    <div class="flex-shrink-0">
                        <Link :href="route('homepage')" class="text-xl font-light tracking-wider hover:text-gray-600 transition-colors">
                            KETYI.COM
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex space-x-8">
                        <Link
                            :href="route('gallery.index')"
                            class="text-sm font-light tracking-wide hover:text-gray-600 transition-colors"
                            :class="{ 'text-black': $page.url.startsWith('/gallery'), 'text-gray-500': !$page.url.startsWith('/gallery') }"
                        >
                            GALLERY
                        </Link>
                        <Link
                            :href="route('about')"
                            class="text-sm font-light tracking-wide hover:text-gray-600 transition-colors"
                            :class="{ 'text-black': $page.url === '/about', 'text-gray-500': $page.url !== '/about' }"
                        >
                            ABOUT
                        </Link>
                        <Link
                            :href="route('contact')"
                            class="text-sm font-light tracking-wide hover:text-gray-600 transition-colors"
                            :class="{ 'text-black': $page.url === '/contact', 'text-gray-500': $page.url !== '/contact' }"
                        >
                            CONTACT
                        </Link>
                        <Link
                            :href="route('shop.index')"
                            class="text-sm font-light tracking-wide hover:text-gray-600 transition-colors"
                            :class="{ 'text-black': $page.url.startsWith('/shop'), 'text-gray-500': !$page.url.startsWith('/shop') }"
                        >
                            SHOP
                        </Link>
                        <Link
                            :href="route('cart.index')"
                            class="text-sm font-light tracking-wide hover:text-gray-600 transition-colors relative"
                            :class="{ 'text-black': $page.url.startsWith('/cart'), 'text-gray-500': !$page.url.startsWith('/cart') }"
                        >
                            CART
                            <span
                                v-if="$page.props.cartCount > 0"
                                class="absolute -top-2 -right-3 bg-black text-white text-xs w-5 h-5 flex items-center justify-center rounded-full"
                            >
                                {{ $page.props.cartCount }}
                            </span>
                        </Link>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button
                            type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="text-gray-600 hover:text-black focus:outline-none"
                        >
                            <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <Link
                        :href="route('gallery.index')"
                        class="block px-3 py-2 text-sm font-light tracking-wide hover:text-black transition-colors"
                        :class="{ 'text-black': $page.url.startsWith('/gallery'), 'text-gray-500': !$page.url.startsWith('/gallery') }"
                        @click="mobileMenuOpen = false"
                    >
                        GALLERY
                    </Link>
                    <Link
                        :href="route('about')"
                        class="block px-3 py-2 text-sm font-light tracking-wide hover:text-black transition-colors"
                        :class="{ 'text-black': $page.url === '/about', 'text-gray-500': $page.url !== '/about' }"
                        @click="mobileMenuOpen = false"
                    >
                        ABOUT
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="block px-3 py-2 text-sm font-light tracking-wide hover:text-black transition-colors"
                        :class="{ 'text-black': $page.url === '/contact', 'text-gray-500': $page.url !== '/contact' }"
                        @click="mobileMenuOpen = false"
                    >
                        CONTACT
                    </Link>
                    <Link
                        :href="route('shop.index')"
                        class="block px-3 py-2 text-sm font-light tracking-wide hover:text-black transition-colors"
                        :class="{ 'text-black': $page.url.startsWith('/shop'), 'text-gray-500': !$page.url.startsWith('/shop') }"
                        @click="mobileMenuOpen = false"
                    >
                        SHOP
                    </Link>
                    <Link
                        :href="route('cart.index')"
                        class="block px-3 py-2 text-sm font-light tracking-wide hover:text-black transition-colors relative"
                        :class="{ 'text-black': $page.url.startsWith('/cart'), 'text-gray-500': !$page.url.startsWith('/cart') }"
                        @click="mobileMenuOpen = false"
                    >
                        CART
                        <span
                            v-if="$page.props.cartCount > 0"
                            class="ml-2 bg-black text-white text-xs px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.cartCount }}
                        </span>
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Placeholder -->
                    <div class="mb-4"></div>

                    <!-- Navigation Links -->
                    <div>
                        <h3 class="text-sm font-light tracking-wide mb-4">EXPLORE</h3>
                        <div class="space-y-2">
                            <Link :href="route('gallery.index')" class="block text-sm text-gray-600 hover:text-black transition-colors">
                                Gallery
                            </Link>
                            <Link :href="route('about')" class="block text-sm text-gray-600 hover:text-black transition-colors">
                                About
                            </Link>
                            <Link :href="route('contact')" class="block text-sm text-gray-600 hover:text-black transition-colors">
                                Contact
                            </Link>
                            <Link :href="route('shop.index')" class="block text-sm text-gray-600 hover:text-black transition-colors">
                                Shop
                            </Link>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div>
                        <h3 class="text-sm font-light tracking-wide mb-4">CONNECT</h3>
                        <div class="flex space-x-4">
                            <a href="https://www.instagram.com/ketyidody/" target="_blank" class="text-gray-600 hover:text-black transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            <a href="https://www.facebook.com/jozef.ketyi/" target="_blank" class="text-gray-600 hover:text-black transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600">&copy; {{ new Date().getFullYear() }} All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
