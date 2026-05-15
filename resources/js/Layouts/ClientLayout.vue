<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});
const isMobileMenuOpen = ref(false);
const searchQuery = ref('');
const showBackToTop = ref(false);

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 500;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('category.show'), { search: searchQuery.value });
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};
</script>

<template>
    <div class="min-h-screen bg-white font-sans text-gray-900 overflow-x-hidden">
        <!-- Top Bar -->
        <div class="bg-[#d10000] text-white text-[10px] md:text-xs py-2 px-4 flex justify-between items-center overflow-hidden">
            <div class="hidden md:block truncate">{{ settings.site_description || 'Chào mừng bạn đến với cửa hàng của chúng tôi!' }}</div>
            <div class="flex gap-2 md:gap-4 mx-auto md:mx-0 font-bold uppercase tracking-tighter md:tracking-widest flex-wrap justify-center">
                <span>Hotline: {{ settings.contact_phone || '1900 2697' }}</span>
                <div class="flex gap-2">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="hover:underline">Tài khoản</Link>
                    <template v-else>
                        <Link :href="route('login')" class="hover:underline">Đăng nhập</Link>
                        <span>/</span>
                        <Link :href="route('register')" class="hover:underline">Đăng ký</Link>
                    </template>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="border-b sticky top-0 bg-white z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 h-16 md:h-20 flex items-center justify-between">
                <!-- Mobile Menu Toggle -->
                <button @click="isMobileMenuOpen = true" class="lg:hidden p-2 hover:text-[#d10000]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>

                <!-- Logo -->
                <div class="flex-shrink-0">
                    <Link :href="'/'">
                        <img v-if="settings.logo" :src="settings.logo" :alt="settings.site_name" class="h-10 md:h-12" />
                        <span v-else class="text-xl md:text-2xl font-bold italic text-[#d10000]">{{ settings.site_name || 'WEB DONG HO' }}</span>
                    </Link>
                </div>

                <!-- Navigation (Desktop) -->
                <nav class="hidden lg:flex space-x-8 text-sm font-bold uppercase tracking-widest">
                    <Link :href="'/'" class="hover:text-[#d10000] transition" :class="{ 'text-[#d10000]': $page.url === '/' }">Trang Chủ</Link>
                    <Link :href="route('category.show', 'dong-ho-nu')" class="hover:text-[#d10000] transition" :class="{ 'text-[#d10000]': $page.url.includes('dong-ho-nu') }">Đồng Hồ Nữ</Link>
                    <Link :href="route('category.show', 'dong-ho-nam')" class="hover:text-[#d10000] transition" :class="{ 'text-[#d10000]': $page.url.includes('dong-ho-nam') }">Đồng Hồ Nam</Link>
                    <Link :href="route('category.show')" class="hover:text-[#d10000] transition" :class="{ 'text-[#d10000]': $page.url === '/category' }">Sản phẩm</Link>
                    <Link :href="route('news.index')" class="hover:text-[#d10000] transition" :class="{ 'text-[#d10000]': $page.url.includes('/news') }">Tin tức</Link>
                </nav>

                <!-- Icons -->
                <div class="flex items-center space-x-2 md:space-x-5">
                    <!-- Desktop Search -->
                    <div class="hidden lg:flex items-center relative">
                        <input 
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text" 
                            placeholder="Tìm kiếm đồng hồ..." 
                            class="border-gray-300 focus:border-[#d10000] focus:ring-[#d10000] text-xs py-1.5 px-4 rounded-full w-60 transition-all"
                        />
                        <button @click="handleSearch" class="absolute right-3 text-gray-400 hover:text-[#d10000]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                    </div>

                    <!-- Wishlist Icon -->
                    <Link v-if="$page.props.auth.user" :href="route('wishlist.index')" class="relative p-2 text-gray-400 hover:text-[#d10000] transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </Link>

                    <Link :href="route('cart.index')" class="relative p-2 hover:text-[#d10000] transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <span v-if="cart.count > 0" class="absolute top-1 right-1 bg-[#d10000] text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full font-bold">{{ cart.count }}</span>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Mobile Menu Overlay -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-x-full"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-x-0"
            leave-to-class="opacity-0 -translate-x-full"
        >
            <div v-if="isMobileMenuOpen" class="fixed inset-0 z-[100] lg:hidden">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="isMobileMenuOpen = false"></div>
                <div class="absolute inset-y-0 left-0 w-80 bg-white shadow-2xl flex flex-col">
                    <div class="p-6 border-b flex justify-between items-center bg-gray-50">
                        <span class="font-bold text-[#d10000] italic tracking-widest uppercase text-sm">Danh Mục Menu</span>
                        <button @click="isMobileMenuOpen = false" class="text-gray-400 hover:text-black">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto py-6 space-y-2">
                        <!-- Mobile Search -->
                        <div class="px-6 mb-6">
                            <div class="relative">
                                <input 
                                    v-model="searchQuery"
                                    @keyup.enter="handleSearch(); isMobileMenuOpen = false"
                                    type="text" 
                                    placeholder="Tìm sản phẩm..." 
                                    class="w-full border-gray-300 focus:border-[#d10000] focus:ring-[#d10000] text-sm px-4 py-2 rounded-lg"
                                />
                                <button @click="handleSearch(); isMobileMenuOpen = false" class="absolute right-3 top-2.5 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </button>
                            </div>
                        </div>

                        <Link href="/" @click="isMobileMenuOpen = false" class="flex items-center px-6 py-4 text-sm font-bold uppercase tracking-widest border-b border-gray-50 hover:bg-gray-50 hover:text-[#d10000]">Trang Chủ</Link>
                        <Link :href="route('category.show', 'dong-ho-nu')" @click="isMobileMenuOpen = false" class="flex items-center px-6 py-4 text-sm font-bold uppercase tracking-widest border-b border-gray-50 hover:bg-gray-50 hover:text-[#d10000]">Đồng Hồ Nữ</Link>
                        <Link :href="route('category.show', 'dong-ho-nam')" @click="isMobileMenuOpen = false" class="flex items-center px-6 py-4 text-sm font-bold uppercase tracking-widest border-b border-gray-50 hover:bg-gray-50 hover:text-[#d10000]">Đồng Hồ Nam</Link>
                        <Link :href="route('category.show')" @click="isMobileMenuOpen = false" class="flex items-center px-6 py-4 text-sm font-bold uppercase tracking-widest border-b border-gray-50 hover:bg-gray-50 hover:text-[#d10000]">Tất Cả Sản Phẩm</Link>
                        <Link v-if="$page.props.auth.user" :href="route('wishlist.index')" @click="isMobileMenuOpen = false" class="flex items-center px-6 py-4 text-sm font-bold uppercase tracking-widest border-b border-gray-50 hover:bg-gray-50 hover:text-[#d10000]">Yêu thích</Link>
                        <Link :href="route('cart.index')" @click="isMobileMenuOpen = false" class="flex items-center px-6 py-4 text-sm font-bold uppercase tracking-widest border-b border-gray-50 hover:bg-gray-50 hover:text-[#d10000]">Giỏ Hàng</Link>
                        <Link :href="route('news.index')" @click="isMobileMenuOpen = false" class="flex items-center px-6 py-4 text-sm font-bold uppercase tracking-widest border-b border-gray-50 hover:bg-gray-50 hover:text-[#d10000]">Tin tức & Blog</Link>
                    </div>
                    <div class="p-6 bg-gray-50 border-t space-y-4">
                        <div class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-[#d10000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            <span class="text-xs font-bold">{{ settings.contact_phone || '1900 2697' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-[#111] text-gray-400 py-16 mt-20">
            <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-12 text-sm">
                <div>
                    <img v-if="settings.logo" :src="settings.logo" class="h-10 mb-6 brightness-0 invert" />
                    <p class="leading-relaxed">{{ settings.site_description || 'Thương hiệu đồng hồ uy tín chất lượng.' }}</p>
                </div>
                <div>
                    <h4 class="text-white font-bold uppercase mb-6 tracking-widest">Hỗ Trợ Khách Hàng</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-white">Chính sách bảo hành</a></li>
                        <li><a href="#" class="hover:text-white">Hướng dẫn mua hàng</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold uppercase mb-6 tracking-widest">Liên Hệ</h4>
                    <p>Địa chỉ: {{ settings.address || 'Hồ Chí Minh, Việt Nam' }}</p>
                    <p class="mt-4">Email: {{ settings.contact_email || 'info@example.com' }}</p>
                    <p class="mt-4">Hotline: {{ settings.contact_phone || '1900 2697' }}</p>
                </div>
                <div>
                    <h4 class="text-white font-bold uppercase mb-6 tracking-widest">Đăng Ký Nhận Tin</h4>
                    <div class="flex mt-4">
                        <input type="email" placeholder="Email của bạn" class="bg-white/10 border-none px-4 py-2 flex-1 focus:ring-1 ring-[#d10000]" />
                        <button class="bg-[#d10000] text-white px-4 py-2">GỬI</button>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 mt-16 pt-8 border-t border-white/10 text-center">
                &copy; 2024 {{ settings.site_name || 'WEB DONG HO' }}. All Rights Reserved.
            </div>
        </footer>

        <!-- Notification Toast -->
        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="cart.notification.show" class="fixed bottom-5 right-5 z-[200] max-w-sm w-full bg-white shadow-2xl rounded-lg border-l-4 border-green-500 overflow-hidden pointer-events-auto">
                <div class="p-4 flex items-start gap-4">
                    <div v-if="cart.notification.product" class="w-12 h-12 shrink-0 border border-gray-100">
                        <img :src="cart.notification.product.image" class="w-full h-full object-cover" />
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-green-600">Thành công!</h3>
                            <button @click="cart.notification.show = false" class="text-gray-400 hover:text-black">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <p class="text-[13px] text-gray-900 font-medium">{{ cart.notification.message }}</p>
                        <div class="mt-3 flex gap-3">
                            <Link :href="route('cart.index')" class="text-[11px] font-bold uppercase tracking-widest text-[#d10000] hover:underline">Xem giỏ hàng</Link>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Floating Action Buttons -->
        <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-4">
            <!-- Contact Buttons -->
            <div class="flex flex-col gap-3">
                <a v-if="settings.contact_phone" :href="'tel:' + settings.contact_phone" class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition group relative overflow-hidden">
                    <img v-if="settings.contact_phone_icon" :src="settings.contact_phone_icon" class="w-full h-full object-cover" />
                    <svg v-else class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    <span class="absolute right-full mr-3 bg-black text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Gọi ngay: {{ settings.contact_phone }}</span>
                </a>
                <a v-if="settings.contact_zalo" :href="settings.contact_zalo" target="_blank" class="w-12 h-12 bg-[#0068ff] text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition group relative overflow-hidden">
                    <img v-if="settings.contact_zalo_icon" :src="settings.contact_zalo_icon" class="w-full h-full object-cover" />
                    <span v-else class="font-bold text-xs italic">Zalo</span>
                    <span class="absolute right-full mr-3 bg-black text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Chat Zalo</span>
                </a>
                <a v-if="settings.contact_messenger" :href="settings.contact_messenger" target="_blank" class="w-12 h-12 bg-[#0084ff] text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition group relative overflow-hidden">
                    <img v-if="settings.contact_messenger_icon" :src="settings.contact_messenger_icon" class="w-full h-full object-cover" />
                    <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.913 1.455 5.51 3.734 7.263.196.15.31.385.31.636 0 .474-.236 1.344-.64 2.228-.086.19.103.38.283.29 1.118-.553 2.164-1.285 2.68-1.57.195-.107.426-.118.63-.03 1.127.487 2.378.76 3.703.76 5.523 0 10-4.145 10-9.258S17.523 2 12 2z" /></svg>
                    <span class="absolute right-full mr-3 bg-black text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Messenger</span>
                </a>
            </div>

            <!-- Back to Top Button -->
            <button 
                v-show="showBackToTop"
                @click="scrollToTop"
                class="w-12 h-12 bg-black text-white rounded-full flex items-center justify-center shadow-lg hover:bg-[#d10000] transition animate-bounce-subtle"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
            </button>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
body {
    font-family: 'Inter', sans-serif;
}
@keyframes bounce-subtle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
.animate-bounce-subtle {
    animation: bounce-subtle 2s infinite;
}
</style>
