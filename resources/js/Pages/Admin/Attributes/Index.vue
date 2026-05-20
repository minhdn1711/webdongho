<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    attributes: Object,
});

const deleteAttribute = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa thuộc tính này?')) {
        router.delete(`/admin/attributes/${id}`);
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Quản lý Thuộc tính" />

        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold text-[#1d2327]">Thuộc tính sản phẩm</h1>
                <Link href="/admin/attributes/create" class="bg-[#2271b1] text-white px-4 py-2 text-sm rounded hover:bg-[#135e96]">
                    + Thêm Thuộc tính
                </Link>
            </div>

            <div class="bg-white border border-[#c3c4c7] shadow-sm">
                <table class="min-w-full divide-y divide-[#c3c4c7]">
                    <thead class="bg-[#f0f0f1]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#1d2327] uppercase">Tên</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#1d2327] uppercase">Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#1d2327] uppercase">Loại</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#1d2327] uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c3c4c7]">
                        <tr v-for="attribute in props.attributes.data" :key="attribute.id" class="hover:bg-[#f6f7f7]">
                            <td class="px-4 py-3 text-sm font-medium text-[#1d2327]">{{ attribute.name }}</td>
                            <td class="px-4 py-3 text-sm text-[#50575e]">{{ attribute.slug }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-semibold">{{ attribute.type }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm space-x-3">
                                <Link :href="`/admin/attributes/${attribute.id}/values`" class="text-[#2271b1] hover:underline">Giá trị</Link>
                                <Link :href="`/admin/attributes/${attribute.id}/edit`" class="text-[#2271b1] hover:underline">Sửa</Link>
                                <button @click="deleteAttribute(attribute.id)" class="text-red-600 hover:underline">Xóa</button>
                            </td>
                        </tr>
                        <tr v-if="!props.attributes.data.length">
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-[#50575e]">Chưa có thuộc tính nào.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
