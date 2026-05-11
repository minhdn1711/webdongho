<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    orders: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusBadge = (status) => {
    const badges = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipping: 'bg-indigo-100 text-indigo-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Chờ xử lý',
        processing: 'Đang xử lý',
        shipping: 'Đang giao',
        completed: 'Hoàn thành',
        cancelled: 'Đã hủy',
    };
    return labels[status] || status;
};

const updateStatus = (orderId, status) => {
    router.patch(route('admin.orders.update-status', orderId), { status });
};
</script>

<template>
    <Head title="Quản lý đơn hàng" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quản lý đơn hàng</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-xs uppercase font-bold tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-6 py-3 text-left">Mã đơn</th>
                                    <th class="px-6 py-3 text-left">Khách hàng</th>
                                    <th class="px-6 py-3 text-left">Tổng tiền</th>
                                    <th class="px-6 py-3 text-left">Trạng thái</th>
                                    <th class="px-6 py-3 text-left">Ngày đặt</th>
                                    <th class="px-6 py-3 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">
                                        <Link :href="route('admin.orders.show', order.id)">{{ order.order_number }}</Link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ order.customer_name }}</div>
                                        <div class="text-xs text-gray-500">{{ order.customer_phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-red-600">
                                        {{ formatPrice(order.total_amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusBadge(order.status)" class="px-2.5 py-0.5 rounded-full text-xs font-bold">
                                            {{ getStatusLabel(order.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ formatDate(order.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <select 
                                                @change="updateStatus(order.id, $event.target.value)"
                                                :value="order.status"
                                                class="text-xs rounded border-gray-300 py-1 pl-2 pr-8 focus:ring-indigo-500"
                                            >
                                                <option value="pending">Chờ xử lý</option>
                                                <option value="processing">Đang xử lý</option>
                                                <option value="shipping">Đang giao</option>
                                                <option value="completed">Hoàn thành</option>
                                                <option value="cancelled">Đã hủy</option>
                                            </select>
                                            <Link :href="route('admin.orders.show', order.id)" class="text-indigo-600 hover:text-indigo-900 font-bold uppercase text-xs border border-indigo-600 px-2 py-1 rounded">Chi tiết</Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="orders.length === 0" class="text-center py-12 text-gray-500 italic">
                            Chưa có đơn hàng nào.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
