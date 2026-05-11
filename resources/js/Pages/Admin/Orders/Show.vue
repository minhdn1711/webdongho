<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
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

const updateStatus = (status) => {
    router.patch(route('admin.orders.update-status', props.order.id), { status });
};

const deleteOrder = () => {
    if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
        router.delete(route('admin.orders.destroy', props.order.id));
    }
};
</script>

<template>
    <Head title="Chi tiết đơn hàng" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chi tiết đơn hàng #{{ order.order_number }}</h2>
                <Link :href="route('admin.orders.index')" class="text-sm text-gray-600 hover:text-black">← Quay lại danh sách</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Order Items -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold mb-6 uppercase tracking-wider border-b pb-4">Danh sách sản phẩm</h3>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="text-xs text-gray-500 uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="py-3 text-left">Sản phẩm</th>
                                        <th class="py-3 text-center">Giá</th>
                                        <th class="py-3 text-center">SL</th>
                                        <th class="py-3 text-right">Tổng</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-sm">
                                    <tr v-for="item in order.items" :key="item.id">
                                        <td class="py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-gray-100 border shrink-0 overflow-hidden rounded">
                                                    <img v-if="item.product" :src="item.product.image" class="w-full h-full object-cover" />
                                                </div>
                                                <span class="font-medium">{{ item.product_name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 text-center">{{ formatPrice(item.price) }}</td>
                                        <td class="py-4 text-center">{{ item.quantity }}</td>
                                        <td class="py-4 text-right font-bold text-red-600">{{ formatPrice(item.price * item.quantity) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="border-t-2 border-gray-100">
                                    <tr class="font-bold text-lg text-gray-900">
                                        <td colspan="3" class="py-6 text-right">TỔNG CỘNG:</td>
                                        <td class="py-6 text-right text-red-600">{{ formatPrice(order.total_amount) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div v-if="order.notes" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold mb-4 uppercase tracking-wider">Ghi chú khách hàng</h3>
                            <p class="text-gray-700 italic bg-gray-50 p-4 border-l-4 border-indigo-500">{{ order.notes }}</p>
                        </div>
                    </div>

                    <!-- Customer Info & Status -->
                    <div class="space-y-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold mb-6 uppercase tracking-wider border-b pb-4">Thông tin giao hàng</h3>
                            <div class="space-y-4 text-sm">
                                <div>
                                    <span class="block text-gray-500 font-bold text-[10px] uppercase">Họ tên</span>
                                    <span class="font-bold text-gray-900">{{ order.customer_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 font-bold text-[10px] uppercase">Số điện thoại</span>
                                    <span class="font-bold text-gray-900">{{ order.customer_phone }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 font-bold text-[10px] uppercase">Email</span>
                                    <span class="font-bold text-gray-900">{{ order.customer_email || 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 font-bold text-[10px] uppercase">Địa chỉ</span>
                                    <span class="font-bold text-gray-900">{{ order.customer_address }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 font-bold text-[10px] uppercase">Ngày đặt hàng</span>
                                    <span class="font-bold text-gray-900">{{ formatDate(order.created_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold mb-6 uppercase tracking-wider border-b pb-4">Xử lý đơn hàng</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Trạng thái hiện tại</label>
                                    <select 
                                        @change="updateStatus($event.target.value)"
                                        :value="order.status"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="pending">Chờ xử lý</option>
                                        <option value="processing">Đang xử lý</option>
                                        <option value="shipping">Đang giao</option>
                                        <option value="completed">Hoàn thành</option>
                                        <option value="cancelled">Đã hủy</option>
                                    </select>
                                </div>

                                <div class="pt-6 border-t border-gray-100">
                                    <button 
                                        @click="deleteOrder"
                                        class="w-full text-red-600 hover:bg-red-50 py-2 border border-red-200 rounded text-xs font-bold uppercase transition"
                                    >
                                        Xóa đơn hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
