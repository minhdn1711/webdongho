<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref('success'); // 'success' or 'error'

const displayNotification = (message, type = 'success') => {
    notificationMessage.value = message;
    notificationType.value = type;
    showNotification.value = true;
    
    setTimeout(() => {
        showNotification.value = false;
    }, 5000);
};

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        displayNotification(flash.success, 'success');
    } else if (flash.error) {
        displayNotification(flash.error, 'error');
    } else if (flash.message) {
        displayNotification(flash.message, 'success');
    }
}, { deep: true });

onMounted(() => {
    if (page.props.flash.success) {
        displayNotification(page.props.flash.success, 'success');
    } else if (page.props.flash.error) {
        displayNotification(page.props.flash.error, 'error');
    }
});

const isSidebarOpen = ref(true);
const showingNavigationDropdown = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const menuItems = [
    { name: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', url: '/admin/dashboard' },
    { name: 'Sản phẩm', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', url: '/admin/products' },
    { name: 'Danh mục', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', url: '/admin/categories' },
    { name: 'Kho hàng', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', url: '/admin/stock' },
    { name: 'Đơn hàng', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', url: '/admin/orders' },
    { name: 'Tin tức', icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM7 8h4m-4 4h8m-8 4h8', url: '/admin/posts' },
    { name: 'Hình ảnh', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', url: '/admin/images' },
    { name: 'Cấu hình', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', url: '/admin/settings' },
    { name: 'Pancake Settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', url: '/admin/pancake/settings' },
    { name: 'Sync Logs', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', url: '/admin/pancake/sync-logs' },
    { name: 'Webhook Logs', icon: 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', url: '/admin/pancake/webhook-logs' },
];
</script>

<template>
    <div class="flex h-screen bg-gray-100 font-sans">
        <!-- Sidebar -->
        <aside 
            :class="isSidebarOpen ? 'w-64' : 'w-16'" 
            class="bg-[#23282d] text-gray-300 transition-all duration-300 ease-in-out hidden md:flex flex-col"
        >
            <!-- Sidebar Header / Logo -->
            <div class="h-12 flex items-center px-4 bg-[#191e23]">
                <Link :href="route('dashboard')" class="flex items-center gap-2 overflow-hidden">
                    <svg class="w-8 h-8 text-blue-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.158 12.786l-2.698 7.84c.806.236 1.657.365 2.54.365 1.047 0 2.05-.176 2.986-.5l-2.828-7.705zm-7.423-4.13c-.636 1.01-.99 2.197-.99 3.468 0 2.235 1.096 4.214 2.78 5.43l3.352-9.155c-1.92.053-3.92.112-5.142.257zm14.398.257c-1.127-.133-2.617-.234-4.22-.257l2.87 7.828c1.644-1.218 2.704-3.176 2.704-5.38 0-1.157-.29-2.247-.803-3.193l.003-.003zM12 1a11 11 0 100 22 11 11 0 000-22zm.072 1.458c.84 0 1.63.14 2.366.4l-1.037 2.876c-.052.146-.118.252-.194.316-.077.065-.184.098-.32.098h-.123c-.15 0-.272-.03-.362-.09-.09-.058-.15-.157-.183-.298L11.026 2.88c.32-.016.643-.024.97-.024l.076.002zM3.488 12c0-1.745.503-3.373 1.37-4.75l.135-.022c1.23-.2 3.51-.314 5.75-.314l.11.353c.033.11.05.213.05.304 0 .227-.122.44-.366.64-.244.198-.6.388-1.066.57-.468.182-1.02.348-1.657.5-1.15.273-2.316.514-2.895.66l-.074.02.046.126 2.934 8.01c-.13.06-.264.123-.4.18L4.17 14.542C3.73 13.736 3.488 12.906 3.488 12zm13.116 5.56l-3.352-9.143c2.05-.05 4.364-.025 5.518.064l.156.012c-.1.312-.224.614-.37 9.067l-1.952-.525zm2.532-6.522c0 1.54-.42 2.983-1.153 4.22L15.65 7.64c.905.02 1.83.055 2.65.132.535.048.9.1 1.096.162.467.148.718.324.756.52.003.016.004.032.004.047 0 .153-.058.28-.175.38s-.28.167-.492.204c-.167.03-.43.045-.79.045l-.56-.007z"/></svg>
                    <span v-show="isSidebarOpen" class="text-white font-bold text-lg tracking-tight">Minhdn</span>
                </Link>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 mt-4 overflow-y-auto no-scrollbar">
                <template v-for="item in menuItems" :key="item.name">
                    <Link 
                        :href="item.url" 
                        class="flex items-center px-4 py-2 hover:bg-[#2c3338] hover:text-blue-400 transition-colors group"
                        :class="{ 'bg-blue-600 text-white': $page.url === item.url }"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        <span v-show="isSidebarOpen" class="ml-3 text-sm font-medium">{{ item.name }}</span>
                    </Link>
                </template>
            </nav>

            <!-- Collapse Toggle -->
            <button 
                @click="isSidebarOpen = !isSidebarOpen"
                class="p-4 hover:bg-[#2c3338] text-gray-400 flex items-center justify-center border-t border-gray-700"
            >
                <svg v-if="isSidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
            </button>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-12 bg-[#191e23] flex items-center justify-between px-4 text-gray-300 z-10 shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="isSidebarOpen = !isSidebarOpen" class="md:hidden p-1 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16m-7 6h7" /></svg>
                    </button>
                    <Link :href="'/'" class="flex items-center gap-2 hover:text-white cursor-pointer group">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
                        <span class="text-sm font-medium">Xem trang chủ</span>
                    </Link>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center gap-2 hover:text-white transition-colors py-1">
                                    <span class="text-sm font-medium">Chào, {{ $page.props.auth.user.name }}</span>
                                    <img 
                                        :src="'https://ui-avatars.com/api/?name=' + $page.props.auth.user.name + '&background=random'" 
                                        class="w-6 h-6 rounded-full border border-gray-600"
                                    />
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

            <!-- Page Body -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
                <!-- Page Heading -->
                <div v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </div>

                <!-- Main Content Slot -->
                <slot />
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6 text-center text-sm text-gray-500 shrink-0">
                <p>&copy; 2024 WebDongHo - Hệ thống quản trị chuyên nghiệp.</p>
            </footer>
        </div>

        <!-- Notification Toast -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showNotification" class="fixed bottom-5 right-5 z-[100] max-w-sm w-full bg-white shadow-2xl rounded-lg border-l-4 overflow-hidden" :class="notificationType === 'success' ? 'border-green-500' : 'border-red-500'">
                <div class="p-4 flex items-center">
                    <div class="shrink-0">
                        <svg v-if="notificationType === 'success'" class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                        <svg v-else class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ notificationMessage }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="showNotification = false" class="inline-flex text-gray-400 hover:text-gray-500">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
