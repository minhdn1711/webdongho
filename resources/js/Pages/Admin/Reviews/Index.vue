<script setup>
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    reviews: Object,
});

const approve = (id) => {
    router.post(route('admin.reviews.approve', id), {}, {
        preserveScroll: true,
    });
};

const reject = (id) => {
    router.post(route('admin.reviews.reject', id), {}, {
        preserveScroll: true,
    });
};

const destroy = (id) => {
    if (confirm('Xóa đánh giá này?')) {
        router.delete(route('admin.reviews.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AdminLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Quản lý Đánh giá</h1>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khách hàng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đánh giá</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="review in reviews.data" :key="review.id">
                            <td class="px-6 py-4 text-sm">{{ review.product?.name || '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                {{ review.customer_name || review.user?.name || 'Khách' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex text-yellow-400">
                                    <span v-for="i in 5" :key="i" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'">★</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm max-w-xs truncate">{{ review.comment }}</td>
                            <td class="px-6 py-4">
                                <span v-if="review.is_approved" class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Đã duyệt</span>
                                <span v-else class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Chờ duyệt</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button v-if="!review.is_approved" @click="approve(review.id)"
                                    class="text-green-600 hover:text-green-800 text-sm">Duyệt</button>
                                <button v-if="review.is_approved" @click="reject(review.id)"
                                    class="text-yellow-600 hover:text-yellow-800 text-sm">Từ chối</button>
                                <button @click="destroy(review.id)"
                                    class="text-red-600 hover:text-red-800 text-sm">Xóa</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4" v-if="reviews.links">
                <div class="flex gap-1">
                    <a v-for="link in reviews.links" :key="link.label" :href="link.url"
                        v-html="link.label"
                        class="px-3 py-1 border text-sm"
                        :class="{ 'bg-gray-200': link.active, 'pointer-events-none text-gray-400': !link.url }">
                    </a>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
