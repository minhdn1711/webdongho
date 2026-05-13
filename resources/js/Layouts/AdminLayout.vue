<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const page = usePage();
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref('success');

const displayNotification = (message, type = 'success') => {
    notificationMessage.value = message;
    notificationType.value = type;
    showNotification.value = true;
    setTimeout(() => { showNotification.value = false; }, 5000);
};

watch(() => page.props.flash, (flash) => {
    if (flash?.success) displayNotification(flash.success, 'success');
    else if (flash?.error) displayNotification(flash.error, 'error');
}, { deep: true });

onMounted(() => {
    if (page.props.flash?.success) displayNotification(page.props.flash.success, 'success');
    else if (page.props.flash?.error) displayNotification(page.props.flash.error, 'error');
    
    if (window.innerWidth < 1024) isSidebarOpen.value = false;
});

const isSidebarOpen = ref(true);
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const menuItems = [
    { name: 'Tổng quan', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Sản phẩm', route: 'admin.products.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Danh mục', route: 'admin.categories.index', icon: 'M4 6h16M4 10h16M4 14h16M4 18h16' },
    { name: 'Kho hàng', route: 'admin.stock.index', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
    { name: 'Đơn hàng', route: 'admin.orders.index', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
    { name: 'Bài viết', route: 'admin.posts.index', icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z' },
    { name: 'Hình ảnh', route: 'admin.images.index', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { name: 'Cấu hình', route: 'admin.settings.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 font-sans text-gray-900 dark:text-gray-100">
        <!-- Sidebar Overlay (Mobile) -->
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="['fixed inset-y-0 left-0 z-50 w-72 transform bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 transition-transform duration-300 ease-in-out lg:translate-x-0', isSidebarOpen ? 'translate-x-0' : '-translate-x-full']">
            <div class="flex h-full flex-col">
                <div class="flex h-20 items-center justify-between px-6 border-b border-gray-50 dark:border-gray-800">
                    <Link :href="route('dashboard')" class="flex items-center space-x-3">
                        <ApplicationLogo class="h-10 w-auto text-indigo-600 fill-current" />
                        <span class="font-display text-xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Admin <span class="text-indigo-600">Pro</span></span>
                    </Link>
                </div>

                <nav class="flex-1 space-y-1 px-4 py-6 overflow-y-auto custom-scrollbar">
                    <Link v-for="item in menuItems" :key="item.name" :href="route(item.route)" :class="['group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200', route().current(item.route) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white']">
                        <svg :class="['mr-3 h-5 w-5 transition-colors', route().current(item.route) ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        {{ item.name }}
                    </Link>

                    <div class="pt-6 pb-2">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tích hợp</p>
                    </div>

                    <Link :href="route('admin.pancake.settings')" :class="['group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200', route().current('admin.pancake.*') ? 'bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white']">
                        <svg class="mr-3 h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Pancake POS
                    </Link>
                </nav>

                <div class="p-4 border-t border-gray-50 dark:border-gray-800">
                    <div class="bg-indigo-600 rounded-2xl p-4 text-white shadow-lg shadow-indigo-200">
                        <p class="text-xs font-medium opacity-80 text-center">WebDongHo Pro v1.2</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="['flex flex-col min-h-screen transition-all duration-300 ease-in-out', isSidebarOpen ? 'lg:pl-72' : 'lg:pl-0']">
            <header class="sticky top-0 z-30 h-20 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-800">
                <div class="flex h-full items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button @click="toggleSidebar" class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        </button>
                        <div class="ml-4 hidden sm:block"><slot name="header" /></div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center space-x-3 p-1 rounded-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:outline-none">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-700 dark:text-indigo-400 font-bold border-2 border-white dark:border-gray-800 shadow-sm">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="hidden md:block text-left leading-none">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Quản trị viên</p>
                                    </div>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Hồ sơ </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button"> Đăng xuất </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8"><slot /></main>

            <footer class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-6 px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
                <p>&copy; 2024 WebDongHo Pro. Tất cả quyền lợi được bảo lưu.</p>
            </footer>
        </div>

        <!-- Notifications -->
        <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showNotification" class="fixed bottom-5 right-5 z-[100] max-w-sm w-full bg-white shadow-2xl rounded-lg border-l-4 overflow-hidden" :class="notificationType === 'success' ? 'border-green-500' : 'border-red-500'">
                <div class="p-4 flex items-center">
                    <p class="text-sm font-medium text-gray-900">{{ notificationMessage }}</p>
                    <button @click="showNotification = false" class="ml-auto text-gray-400 hover:text-gray-500"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg></button>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>
