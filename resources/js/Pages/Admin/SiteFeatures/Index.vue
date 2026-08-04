<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ features: Array });

const featureToDelete = ref(null);

const confirmDelete = (feature) => { featureToDelete.value = feature; };
const cancelDelete = () => { featureToDelete.value = null; };
const doDelete = () => {
    router.delete(route('admin.site-features.destroy', featureToDelete.value.id), {
        onFinish: () => { featureToDelete.value = null; }
    });
};

const toggleActive = (feature) => {
    router.patch(route('admin.site-features.update', feature.id), { is_active: !feature.is_active });
};
</script>

<template>
    <Head title="Tính năng nổi bật" />
    <AdminLayout>
        <div class="max-w-4xl mx-auto py-8 px-4">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Tính năng nổi bật (Trang chủ)</h1>
                <Link :href="route('admin.site-features.create')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                    + Thêm mới
                </Link>
            </div>

            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Thứ tự</th>
                            <th class="px-4 py-3 text-left">Tiêu đề</th>
                            <th class="px-4 py-3 text-left">Mô tả</th>
                            <th class="px-4 py-3 text-left">Icon</th>
                            <th class="px-4 py-3 text-center">Hiển thị</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="features.length === 0">
                            <td colspan="6" class="text-center py-10 text-gray-400">Chưa có tính năng nào.</td>
                        </tr>
                        <tr v-for="f in features" :key="f.id" class="hover:bg-gray-50 group">
                            <td class="px-4 py-3 text-gray-500 w-16">{{ f.order }}</td>
                            <td class="px-4 py-3 font-semibold">{{ f.title }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ f.description }}</td>
                            <td class="px-4 py-3">
                                <svg v-if="f.icon_svg" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="f.icon_svg"></svg>
                                <span v-else class="text-gray-300 text-xs">—</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="toggleActive(f)" :class="f.is_active ? 'text-green-600' : 'text-gray-300'" class="text-lg font-bold">
                                    {{ f.is_active ? '●' : '○' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="opacity-0 group-hover:opacity-100 transition flex gap-3 justify-end">
                                    <Link :href="route('admin.site-features.edit', f.id)" class="text-blue-600 hover:underline">Sửa</Link>
                                    <button @click="confirmDelete(f)" class="text-red-500 hover:underline">Xóa</button>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="featureToDelete" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
                <h3 class="font-bold text-lg mb-2">Xác nhận xóa</h3>
                <p class="text-gray-600 mb-6">Bạn có chắc muốn xóa <strong>{{ featureToDelete.title }}</strong>?</p>
                <div class="flex justify-end gap-3">
                    <button @click="cancelDelete" class="px-4 py-2 border rounded hover:bg-gray-50">Hủy</button>
                    <button @click="doDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Xóa</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
