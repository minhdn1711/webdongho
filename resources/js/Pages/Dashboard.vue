<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Bảng điều khiển" />

    <AdminLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-gray-800">Bảng điều khiển</h2>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded shadow-sm border-t-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Đơn hàng mới</h3>
                <p class="text-3xl font-bold mt-2">12</p>
            </div>
            <div class="bg-white p-6 rounded shadow-sm border-t-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Sản phẩm</h3>
                <p class="text-3xl font-bold mt-2">156</p>
            </div>
            <div class="bg-white p-6 rounded shadow-sm border-t-4 border-purple-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Doanh thu</h3>
                <p class="text-3xl font-bold mt-2">45.000.000đ</p>
            </div>
        </div>

        <div class="mt-8 bg-white p-6 rounded shadow-sm">
            <h3 class="text-lg font-bold mb-4">Chào mừng bạn đến với hệ quản trị Web Đồng Hồ</h3>
            <p class="text-gray-600">Tại đây bạn có thể quản lý sản phẩm, đơn hàng và các thiết lập cho website của mình.</p>
        </div>
    </AdminLayout>
</template>
