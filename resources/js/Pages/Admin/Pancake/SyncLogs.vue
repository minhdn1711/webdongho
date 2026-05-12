<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    logs: Object,
});

const retrySync = (id) => {
    router.post(route('admin.pancake.retry-sync', id));
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('vi-VN');
};
</script>

<template>
    <Head title="Lịch sử đồng bộ Pancake" />

    <AdminLayout>
        <template #header>
            <h1 class="text-[23px] font-normal text-[#1d2327]">Lịch sử đồng bộ</h1>
        </template>

        <div class="bg-white border border-[#c3c4c7] shadow-sm overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Thời gian</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Loại</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Hành động</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Trạng thái</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Lỗi</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs.data" :key="log.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] transition">
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">{{ formatDate(log.created_at) }}</td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">
                            {{ log.model_type.split('\\').pop() }} (ID: {{ log.model_id }})
                        </td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e] uppercase font-bold">{{ log.action }}</td>
                        <td class="px-3 py-2 text-[13px]">
                            <span 
                                :class="log.status === 'success' ? 'bg-[#dcfce7] text-[#166534]' : 'bg-[#fee2e2] text-[#991b1b]'"
                                class="px-2 py-0.5 rounded text-[11px] font-semibold"
                            >
                                {{ log.status.toUpperCase() }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-[12px] text-red-500 max-w-xs truncate" :title="log.error_message">
                            {{ log.error_message }}
                        </td>
                        <td class="px-3 py-2">
                            <button 
                                v-if="log.status === 'failed'"
                                @click="retrySync(log.id)" 
                                class="text-[12px] text-[#2271b1] hover:underline"
                            >
                                Thử lại
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!logs.data.length">
                        <td colspan="6" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có lịch sử nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination would go here if needed -->
    </AdminLayout>
</template>
