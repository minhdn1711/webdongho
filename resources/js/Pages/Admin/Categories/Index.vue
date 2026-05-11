<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: Array,
});

const isModalOpen = ref(false);
const editingCategory = ref(null);
const categoryToDelete = ref(null);

const form = useForm({
    name: '',
    image: '',
});

const openCreateModal = () => {
    editingCategory.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.image = category.image || '';
    isModalOpen.value = true;
};

const submit = () => {
    if (editingCategory.value) {
        form.put(route('admin.categories.update', editingCategory.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    isModalOpen.value = false;
    editingCategory.value = null;
    form.reset();
};

const confirmDelete = (category) => {
    categoryToDelete.value = category;
};

const deleteCategory = () => {
    router.delete(route('admin.categories.destroy', categoryToDelete.value.id), {
        onSuccess: () => {
            categoryToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Quản lý danh mục" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h1 class="text-[23px] font-normal text-[#1d2327]">
                    Danh mục
                    <button @click="openCreateModal" class="text-[13px] font-medium text-[#2271b1] hover:text-[#135e96] ml-2 relative top-[-2px]">
                        Thêm mới
                    </button>
                </h1>
            </div>
        </template>

        <div class="bg-white border border-[#c3c4c7] shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Tên danh mục</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Slug</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Số sản phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories" :key="category.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] transition group">
                        <td class="px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#2271b1]">{{ category.name }}</span>
                            <!-- Row Actions (WordPress style) -->
                            <div class="text-[12px] mt-0.5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-[#8c8f94]">
                                <button @click="openEditModal(category)" class="text-[#2271b1] hover:text-[#135e96]">Sửa</button>
                                <span>|</span>
                                <button @click="confirmDelete(category)" class="text-[#b32d2e] hover:text-[#a02929]">Xóa</button>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">{{ category.slug }}</td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">{{ category.products_count }}</td>
                    </tr>
                    <tr v-if="!categories.length">
                        <td colspan="3" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có danh mục nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-4 py-3 rounded-t-lg flex justify-between items-center">
                    <h3 class="text-[14px] font-semibold text-[#1d2327]">{{ editingCategory ? 'Sửa danh mục' : 'Thêm danh mục mới' }}</h3>
                    <button @click="closeModal" class="text-[#8c8f94] hover:text-[#1d2327]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form @submit.prevent="submit" class="p-4 space-y-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Tên danh mục</label>
                        <input v-model="form.name" type="text" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" required />
                        <div v-if="form.errors.name" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Hình ảnh (URL)</label>
                        <input v-model="form.image" type="text" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="https://..." />
                    </div>
                    <div class="pt-2 flex justify-end gap-3 border-t border-[#dcdcde]">
                        <button type="button" @click="closeModal" class="px-4 py-1.5 text-[13px] font-medium text-[#50575e] bg-[#f0f0f1] hover:bg-[#dcdcde] rounded transition">
                            Hủy
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-1.5 text-[13px] font-medium text-white bg-[#2271b1] hover:bg-[#135e96] rounded transition disabled:opacity-50">
                            {{ form.processing ? 'Đang lưu...' : 'Lưu lại' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="categoryToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold mb-2 text-[#1d2327]">Xác nhận xóa</h3>
                <p class="text-[#50575e] text-[14px] mb-6">Bạn có chắc chắn muốn xóa danh mục <strong>"{{ categoryToDelete.name }}"</strong>? Hành động này không thể hoàn tác.</p>
                <div v-if="$page.props.errors.message" class="bg-[#fcf0f1] text-[#d63638] p-3 rounded mb-4 text-[13px]">{{ $page.props.errors.message }}</div>
                <div class="flex justify-end gap-3">
                    <button @click="categoryToDelete = null" class="px-4 py-2 text-[13px] font-medium text-[#50575e] bg-[#f0f0f1] hover:bg-[#dcdcde] rounded transition">
                        Hủy
                    </button>
                    <button @click="deleteCategory" class="px-4 py-2 text-[13px] font-medium text-white bg-[#d63638] hover:bg-[#b32d2e] rounded transition">
                        Xóa ngay
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
