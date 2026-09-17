<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminPagination from '@/Components/AdminPagination.vue';

const props = defineProps({
    menus: { type: Object, default: () => ({ data: [], last_page: 1 }) },
    categories: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
    posts: { type: Array, default: () => [] },
});

const isModalOpen = ref(false);
const editingMenu = ref(null);
const menuToDelete = ref(null);
const form = useForm({
    label: '', source_type: 'category', source_id: '', url: '', sort_order: 0,
    is_active: true, open_new_tab: false,
});

const sourceLabel = (menu) => ({ category: 'Danh mục', product: 'Sản phẩm', post: 'Bài viết', custom: 'Custom link' }[menu.source_type] || menu.source_type);
const sourceName = (menu) => {
    if (menu.source_type === 'category') return menu.category?.name || 'Không tồn tại';
    if (menu.source_type === 'product') return menu.product?.name || 'Không tồn tại';
    if (menu.source_type === 'post') return menu.post?.title || 'Không tồn tại';
    return menu.url;
};

const openCreateModal = () => {
    editingMenu.value = null;
    form.reset();
    form.source_type = 'category';
    form.is_active = true;
    isModalOpen.value = true;
};

const openEditModal = (menu) => {
    editingMenu.value = menu;
    form.label = menu.label;
    form.source_type = menu.source_type;
    form.source_id = menu.source_id || '';
    form.url = menu.url || '';
    form.sort_order = menu.sort_order;
    form.is_active = !!menu.is_active;
    form.open_new_tab = !!menu.open_new_tab;
    isModalOpen.value = true;
};

const submit = () => {
    const options = { onSuccess: () => closeModal() };
    editingMenu.value
        ? form.put(route('admin.menus.update', editingMenu.value.id), options)
        : form.post(route('admin.menus.store'), options);
};

const closeModal = () => {
    isModalOpen.value = false;
    editingMenu.value = null;
    form.reset();
};

const deleteMenu = () => {
    router.delete(route('admin.menus.destroy', menuToDelete.value.id), {
        onSuccess: () => { menuToDelete.value = null; },
    });
};
</script>

<template>
    <Head title="Quản lý menu" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h1 class="text-[23px] font-normal text-[#1d2327]">Menu Header
                    <button @click="openCreateModal" class="text-[13px] font-medium text-[#2271b1] ml-2">Thêm mới</button>
                </h1>
            </div>
        </template>

        <div class="bg-white border border-[#c3c4c7] shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px]">Tên menu</th>
                        <th class="px-3 py-2 text-[13px]">Nguồn</th>
                        <th class="px-3 py-2 text-[13px]">Thứ tự</th>
                        <th class="px-3 py-2 text-[13px]">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="menu in menus.data" :key="menu.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] group">
                        <td class="px-3 py-2 text-[13px] font-semibold text-[#2271b1]">
                            {{ menu.label }}
                            <div class="text-[12px] mt-0.5 opacity-0 group-hover:opacity-100 flex gap-1 text-[#8c8f94]">
                                <button @click="openEditModal(menu)" class="text-[#2271b1]">Sửa</button><span>|</span>
                                <button @click="menuToDelete = menu" class="text-[#b32d2e]">Xóa</button>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">{{ sourceLabel(menu) }}: {{ sourceName(menu) }}</td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">{{ menu.sort_order }}</td>
                        <td class="px-3 py-2 text-[13px]" :class="menu.is_active ? 'text-green-600' : 'text-gray-400'">{{ menu.is_active ? 'Đang hiển thị' : 'Đang ẩn' }}</td>
                    </tr>
                    <tr v-if="!menus.data.length"><td colspan="4" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có mục menu nào.</td></tr>
                </tbody>
            </table>
        </div>

        <AdminPagination :paginator="menus" label="mục menu" />

        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="bg-[#f0f0f1] border-b px-4 py-3 rounded-t-lg flex justify-between items-center">
                    <h3 class="text-[14px] font-semibold">{{ editingMenu ? 'Sửa menu' : 'Thêm menu mới' }}</h3>
                    <button @click="closeModal" class="text-[#8c8f94]">X</button>
                </div>
                <form @submit.prevent="submit" class="p-4 space-y-4">
                    <div>
                        <label class="block text-[12px] font-semibold uppercase mb-1.5">Tên hiển thị</label>
                        <input v-model="form.label" class="w-full border-[#8c8f94] rounded text-[13px]" required />
                        <div v-if="form.errors.label" class="text-red-600 text-[11px]">{{ form.errors.label }}</div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold uppercase mb-1.5">Loại liên kết</label>
                        <select v-model="form.source_type" class="w-full border-[#8c8f94] rounded text-[13px]">
                            <option value="category">Danh mục</option><option value="product">Sản phẩm</option><option value="post">Bài viết</option><option value="custom">Custom link</option>
                        </select>
                    </div>
                    <div v-if="form.source_type !== 'custom'">
                        <label class="block text-[12px] font-semibold uppercase mb-1.5">Chọn nội dung</label>
                        <select v-model="form.source_id" class="w-full border-[#8c8f94] rounded text-[13px]" required>
                            <option value="">-- Chọn --</option>
                            <template v-if="form.source_type === 'category'"><option v-for="item in categories" :key="item.id" :value="item.id">{{ item.name }}</option></template>
                            <template v-if="form.source_type === 'product'"><option v-for="item in products" :key="item.id" :value="item.id">{{ item.name }}</option></template>
                            <template v-if="form.source_type === 'post'"><option v-for="item in posts" :key="item.id" :value="item.id">{{ item.title }}</option></template>
                        </select>
                    </div>
                    <div v-else>
                        <label class="block text-[12px] font-semibold uppercase mb-1.5">URL tùy chỉnh</label>
                        <input v-model="form.url" type="url" placeholder="https://... hoặc /duong-dan" class="w-full border-[#8c8f94] rounded text-[13px]" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-[12px] font-semibold uppercase mb-1.5">Thứ tự</label><input v-model.number="form.sort_order" type="number" min="0" class="w-full border-[#8c8f94] rounded text-[13px]" /></div>
                        <label class="flex items-center gap-2 text-[13px] mt-6"><input v-model="form.is_active" type="checkbox" /> Hiển thị menu</label>
                    </div>
                    <label class="flex items-center gap-2 text-[13px]"><input v-model="form.open_new_tab" type="checkbox" /> Mở liên kết tab mới</label>
                    <div class="pt-2 flex justify-end gap-3 border-t">
                        <button type="button" @click="closeModal" class="px-4 py-1.5 text-[13px] bg-[#f0f0f1] rounded">Hủy</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-1.5 text-[13px] text-white bg-[#2271b1] rounded disabled:opacity-50">{{ form.processing ? 'Đang lưu...' : 'Lưu lại' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="menuToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold mb-2">Xác nhận xóa</h3>
                <p class="text-sm mb-6">Bạn có chắc muốn xóa menu <strong>"{{ menuToDelete.label }}"</strong>?</p>
                <div class="flex justify-end gap-3"><button @click="menuToDelete = null" class="px-4 py-2 text-sm bg-gray-100 rounded">Hủy</button><button @click="deleteMenu" class="px-4 py-2 text-sm text-white bg-red-600 rounded">Xóa ngay</button></div>
            </div>
        </div>
    </AdminLayout>
</template>
