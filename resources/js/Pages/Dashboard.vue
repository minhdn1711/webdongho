<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const stats = [
    { name: 'Đơn hàng mới', value: '12', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', color: 'from-blue-500 to-blue-600' },
    { name: 'Sản phẩm', value: '156', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', color: 'from-emerald-500 to-emerald-600' },
    { name: 'Doanh thu', value: '45.000.000đ', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'from-amber-500 to-amber-600' },
];
</script>

<template>
    <Head title="Bảng điều khiển" />

    <AdminLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold text-gray-800 dark:text-white">Bảng điều khiển</h2>
        </template>

        <div class="space-y-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="stat in stats" :key="stat.name" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 p-6 flex items-center space-x-4 hover:shadow-md transition-shadow">
                    <div :class="['p-3 rounded-xl bg-gradient-to-br text-white shadow-lg', stat.color]">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ stat.name }}</p>
                        <p class="text-2xl font-display font-bold text-gray-900 dark:text-white mt-1">{{ stat.value }}</p>
                    </div>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="bg-white dark:bg-gray-800 p-8 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-2xl font-display font-bold text-gray-900 dark:text-white mb-4">Chào mừng trở lại, {{ $page.props.auth.user.name }}!</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl leading-relaxed">
                        Hệ thống quản trị Web Đồng Hồ Pro đã sẵn sàng. Bạn có thể theo dõi doanh số, quản lý kho hàng và cập nhật các mẫu đồng hồ mới nhất ngay tại đây.
                    </p>
                    <div class="mt-6 flex space-x-3">
                        <Link :href="route('admin.products.index')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Quản lý sản phẩm
                        </Link>
                        <Link :href="route('admin.orders.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                            Xem đơn hàng
                        </Link>
                    </div>
                </div>
                
                <!-- Decorative element -->
                <div class="absolute top-0 right-0 -mt-20 -mr-20 h-64 w-64 bg-indigo-50 dark:bg-indigo-900/10 rounded-full opacity-50"></div>
            </div>
        </div>
    </AdminLayout>
</template>
