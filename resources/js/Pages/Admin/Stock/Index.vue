<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    products: Array,
    histories: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('vi-VN');
};

const selectedProduct = ref(null);
const activeTab = ref('products'); // 'products' or 'history'

const stockForm = useForm({
    type: 'import',
    quantity: 0,
    note: '',
});

const openStockModal = (product) => {
    selectedProduct.value = product;
    stockForm.quantity = 0;
    stockForm.type = 'import';
    stockForm.note = '';
};

const closeStockModal = () => {
    selectedProduct.value = null;
};

const updateStock = () => {
    stockForm.patch(route('admin.stock.update', selectedProduct.value.id), {
        onSuccess: () => {
            closeStockModal();
        },
    });
};
</script>

<template>
    <Head title="Quản lý kho hàng" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h1 class="text-[23px] font-normal text-[#1d2327]">
                    Quản lý kho hàng
                </h1>
                <div class="flex bg-[#dcdcde] p-0.5 rounded shadow-sm">
                    <button 
                        @click="activeTab = 'products'"
                        :class="activeTab === 'products' ? 'bg-white shadow-sm text-[#1d2327]' : 'text-[#50575e] hover:text-[#1d2327]'"
                        class="px-4 py-1 text-[13px] font-medium rounded transition"
                    >
                        Danh sách tồn kho
                    </button>
                    <button 
                        @click="activeTab = 'history'"
                        :class="activeTab === 'history' ? 'bg-white shadow-sm text-[#1d2327]' : 'text-[#50575e] hover:text-[#1d2327]'"
                        class="px-4 py-1 text-[13px] font-medium rounded transition"
                    >
                        Lịch sử xuất nhập
                    </button>
                </div>
            </div>
        </template>

        <div v-if="activeTab === 'products'" class="bg-white border border-[#c3c4c7] shadow-sm animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327] w-16"></th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Sản phẩm</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Danh mục</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Giá</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Tồn kho</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products" :key="product.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] transition group">
                        <td class="px-3 py-2">
                            <img :src="product.image || 'https://via.placeholder.com/60'" class="w-10 h-10 object-cover rounded border border-[#c3c4c7]" />
                        </td>
                        <td class="px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">{{ product.name }}</span>
                            <div class="text-[12px] mt-0.5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-[#8c8f94]">
                                <button @click="openStockModal(product)" class="text-[#2271b1] hover:text-[#135e96]">Điều chỉnh kho</button>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">{{ product.categories?.map(c => c.name).join(', ') || '—' }}</td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">{{ formatPrice(product.price) }}</td>
                        <td class="px-3 py-2 text-[13px]">
                            <span
                                :class="{
                                    'text-[#d63638] font-bold': product.stock === 0,
                                    'text-[#dba617] font-semibold': product.stock > 0 && product.stock <= 5,
                                    'text-[#00a32a]': product.stock > 5,
                                }"
                            >
                                {{ product.stock }}
                            </span>
                            <span v-if="product.stock === 0" class="text-[11px] text-[#d63638] ml-1">Hết hàng</span>
                            <span v-else-if="product.stock <= 5" class="text-[11px] text-[#dba617] ml-1">Sắp hết</span>
                        </td>
                    </tr>
                    <tr v-if="!products.length">
                        <td colspan="5" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có sản phẩm nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="bg-white border border-[#c3c4c7] shadow-sm animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Thời gian</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Sản phẩm</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Loại</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Số lượng</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Tồn cũ → Mới</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Người thực hiện</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in histories" :key="log.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] transition">
                        <td class="px-3 py-2 text-[12px] text-[#50575e]">{{ formatDate(log.created_at) }}</td>
                        <td class="px-3 py-2">
                            <div class="text-[13px] font-medium text-[#1d2327]">{{ log.product?.name }}</div>
                            <div v-if="log.note" class="text-[11px] text-[#8c8f94] italic">{{ log.note }}</div>
                        </td>
                        <td class="px-3 py-2">
                            <span 
                                :class="{
                                    'bg-green-100 text-green-700': log.type === 'import',
                                    'bg-red-100 text-red-700': log.type === 'export',
                                    'bg-blue-100 text-blue-700': log.type === 'set',
                                    'bg-gray-100 text-gray-700': log.type === 'sale',
                                }"
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                            >
                                {{ log.type === 'import' ? 'Nhập kho' : log.type === 'export' ? 'Xuất kho' : log.type === 'set' ? 'Điều chỉnh' : 'Bán hàng' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-[13px] font-semibold">
                            {{ log.type === 'import' ? '+' : log.type === 'export' || log.type === 'sale' ? '-' : '' }}{{ log.quantity }}
                        </td>
                        <td class="px-3 py-2 text-[12px] text-[#50575e]">
                            {{ log.old_stock }} → {{ log.new_stock }}
                        </td>
                        <td class="px-3 py-2 text-[12px] text-[#50575e]">
                            {{ log.user?.name || 'Hệ thống' }}
                        </td>
                    </tr>
                    <tr v-if="!histories.length">
                        <td colspan="6" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có lịch sử giao dịch nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Stock Adjustment Modal -->
        <div v-if="selectedProduct" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-scale-in">
                <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-4 py-3 rounded-t-lg flex justify-between items-center">
                    <h3 class="text-[14px] font-semibold text-[#1d2327]">Điều chỉnh kho</h3>
                    <button @click="closeStockModal" class="text-[#8c8f94] hover:text-[#1d2327]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[#dcdcde]">
                        <img :src="selectedProduct.image || 'https://via.placeholder.com/60'" class="w-12 h-12 object-cover rounded border border-[#c3c4c7]" />
                        <div>
                            <div class="text-[13px] font-semibold text-[#1d2327]">{{ selectedProduct.name }}</div>
                            <div class="text-[12px] text-[#8c8f94]">Tồn kho hiện tại: <strong :class="selectedProduct.stock === 0 ? 'text-[#d63638]' : 'text-[#1d2327]'">{{ selectedProduct.stock }}</strong></div>
                        </div>
                    </div>

                    <form @submit.prevent="updateStock" class="space-y-4">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Loại điều chỉnh</label>
                            <select v-model="stockForm.type" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]">
                                <option value="import">Nhập kho (+)</option>
                                <option value="export">Xuất kho (−)</option>
                                <option value="set">Thiết lập lại (=)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Số lượng</label>
                            <input v-model="stockForm.quantity" type="number" min="0" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                            <div v-if="stockForm.errors.quantity" class="text-[#d63638] text-[11px] mt-1">{{ stockForm.errors.quantity }}</div>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Ghi chú (Tùy chọn)</label>
                            <input v-model="stockForm.note" type="text" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="Ví dụ: Nhập hàng đợt 1, xuất trả hàng..." />
                        </div>

                        <div class="pt-2 flex justify-end gap-3 border-t border-[#dcdcde]">
                            <button type="button" @click="closeStockModal" class="px-4 py-1.5 text-[13px] font-medium text-[#50575e] bg-[#f0f0f1] hover:bg-[#dcdcde] rounded transition">
                                Hủy
                            </button>
                            <button type="submit" :disabled="stockForm.processing" class="px-4 py-1.5 text-[13px] font-medium text-white bg-[#2271b1] hover:bg-[#135e96] rounded transition disabled:opacity-50">
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
.animate-scale-in {
    animation: scaleIn 0.2s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes scaleIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>
