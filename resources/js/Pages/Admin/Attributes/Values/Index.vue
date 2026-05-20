<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    attribute: Object,
    values: Object,
});

const deleteValue = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa giá trị này?')) {
        router.delete(`/admin/attributes/${props.attribute.id}/values/${id}`);
    }
};
</script>

<template>
    <AdminLayout>
        <Head :title="`Giá trị - ${props.attribute.name}`" />

        <div class="space-y-4">
            <div class="flex items-center gap-2 text-sm text-[#50575e]">
                <Link href="/admin/attributes" class="text-[#2271b1] hover:underline">Thuộc tính</Link>
                <span>›</span>
                <span>{{ props.attribute.name }}</span>
            </div>

            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold text-[#1d2327]">Giá trị: {{ props.attribute.name }}</h1>
                <Link :href="`/admin/attributes/${props.attribute.id}/values/create`" class="bg-[#2271b1] text-white px-4 py-2 text-sm rounded hover:bg-[#135e96]">
                    + Thêm Giá trị
                </Link>
            </div>

            <div class="bg-white border border-[#c3c4c7] shadow-sm">
                <table class="min-w-full divide-y divide-[#c3c4c7]">
                    <thead class="bg-[#f0f0f1]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#1d2327] uppercase">Giá trị</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#1d2327] uppercase">Slug</th>
                            <th v-if="props.attribute.type === 'color'" class="px-4 py-3 text-left text-xs font-semibold text-[#1d2327] uppercase">Màu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#1d2327] uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c3c4c7]">
                        <tr v-for="value in props.values.data" :key="value.id" class="hover:bg-[#f6f7f7]">
                            <td class="px-4 py-3 text-sm font-medium text-[#1d2327]">{{ value.value }}</td>
                            <td class="px-4 py-3 text-sm text-[#50575e]">{{ value.slug }}</td>
                            <td v-if="props.attribute.type === 'color'" class="px-4 py-3">
                                <div v-if="value.meta_value" class="flex items-center gap-2">
                                    <div :style="{ backgroundColor: value.meta_value }" class="w-7 h-7 rounded border border-[#c3c4c7]"></div>
                                    <span class="text-xs text-[#50575e]">{{ value.meta_value }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm space-x-3">
                                <Link :href="`/admin/attributes/${props.attribute.id}/values/${value.id}/edit`" class="text-[#2271b1] hover:underline">Sửa</Link>
                                <button @click="deleteValue(value.id)" class="text-red-600 hover:underline">Xóa</button>
                            </td>
                        </tr>
                        <tr v-if="!props.values.data.length">
                            <td :colspan="props.attribute.type === 'color' ? 4 : 3" class="px-4 py-6 text-center text-sm text-[#50575e]">Chưa có giá trị nào.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
