<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    products: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const productToDelete = ref(null);

const confirmDelete = (product) => {
    productToDelete.value = product;
};

const deleteProduct = () => {
    router.delete(route('admin.products.destroy', productToDelete.value.id), {
        onSuccess: () => {
            productToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Quản lý sản phẩm" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h1 class="text-[23px] font-normal text-[#1d2327]">
                    Sản phẩm
                    <Link :href="route('admin.products.create')" class="text-[13px] font-medium text-[#2271b1] hover:text-[#135e96] ml-2 relative top-[-2px]">
                        Thêm mới
                    </Link>
                </h1>
            </div>
        </template>

        <div class="bg-white border border-[#c3c4c7] shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327] w-16"></th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Tên sản phẩm</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Danh mục</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Giá</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Kho</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Nổi bật</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products" :key="product.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] transition group">
                        <td class="px-3 py-2">
                            <img :src="product.image || 'https://via.placeholder.com/60'" class="w-12 h-12 object-cover rounded border border-[#c3c4c7]" />
                        </td>
                        <td class="px-3 py-2">
                            <Link :href="route('admin.products.edit', product.id)" class="text-[13px] font-semibold text-[#2271b1] hover:text-[#135e96] hover:underline">
                                {{ product.name }}
                            </Link>
                            <!-- Row Actions (WordPress style) -->
                            <div class="text-[12px] mt-0.5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-[#8c8f94]">
                                <Link :href="route('admin.products.edit', product.id)" class="text-[#2271b1] hover:text-[#135e96]">Sửa</Link>
                                <span>|</span>
                                <button @click="confirmDelete(product)" class="text-[#b32d2e] hover:text-[#a02929]">Xóa</button>
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <span class="text-[13px] text-[#50575e]">{{ product.category?.name }}</span>
                        </td>
                        <td class="px-3 py-2 text-[13px]">
                            <div v-if="product.sale_price" class="text-[#d63638] font-semibold">{{ formatPrice(product.sale_price) }}</div>
                            <div :class="product.sale_price ? 'line-through text-[11px] text-[#8c8f94]' : 'text-[#1d2327]'">{{ formatPrice(product.price) }}</div>
                        </td>
                        <td class="px-3 py-2 text-[13px]">
                            <span :class="product.stock <= 5 ? 'text-[#d63638] font-bold' : 'text-[#50575e]'">
                                {{ product.stock }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-[13px] text-center">
                            <span v-if="product.is_featured" class="text-[#dba617]">★</span>
                            <span v-else class="text-[#dcdcde]">☆</span>
                        </td>
                    </tr>
                    <tr v-if="!products.length">
                        <td colspan="6" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có sản phẩm nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="productToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold mb-2 text-[#1d2327]">Xác nhận xóa</h3>
                <p class="text-[#50575e] text-[14px] mb-6">Bạn có chắc chắn muốn xóa sản phẩm <strong>"{{ productToDelete.name }}"</strong>? Hành động này không thể hoàn tác.</p>
                <div class="flex justify-end gap-3">
                    <button @click="productToDelete = null" class="px-4 py-2 text-[13px] font-medium text-[#50575e] bg-[#f0f0f1] hover:bg-[#dcdcde] rounded transition">
                        Hủy
                    </button>
                    <button @click="deleteProduct" class="px-4 py-2 text-[13px] font-medium text-white bg-[#d63638] hover:bg-[#b32d2e] rounded transition">
                        Xóa ngay
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
