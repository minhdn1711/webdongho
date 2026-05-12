<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    logs: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleString('vi-VN');
};
</script>

<template>
    <Head title="Lịch sử Webhook Pancake" />

    <AdminLayout>
        <template #header>
            <h1 class="text-[23px] font-normal text-[#1d2327]">Lịch sử Webhook</h1>
        </template>

        <div class="bg-white border border-[#c3c4c7] shadow-sm overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Thời gian</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Topic</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Trạng thái</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Lỗi</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Dữ liệu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs.data" :key="log.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] transition">
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">{{ formatDate(log.created_at) }}</td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e] font-mono">{{ log.topic }}</td>
                        <td class="px-3 py-2 text-[13px]">
                            <span 
                                :class="{
                                    'bg-[#dcfce7] text-[#166534]': log.status === 'processed',
                                    'bg-[#fee2e2] text-[#991b1b]': log.status === 'failed',
                                    'bg-[#fef9c3] text-[#854d0e]': log.status === 'pending'
                                }"
                                class="px-2 py-0.5 rounded text-[11px] font-semibold"
                            >
                                {{ log.status.toUpperCase() }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-[12px] text-red-500 max-w-xs truncate">{{ log.error }}</td>
                        <td class="px-3 py-2">
                            <pre class="text-[10px] bg-[#f6f7f7] p-2 rounded max-h-24 overflow-y-auto w-64">{{ JSON.stringify(log.payload, null, 2) }}</pre>
                        </td>
                    </tr>
                    <tr v-if="!logs.data.length">
                        <td colspan="5" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có webhook nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
