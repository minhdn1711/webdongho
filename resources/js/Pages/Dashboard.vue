<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend);

const props = defineProps({
    stats: Object,
    filters: { type: Object, default: () => ({ range: '7d' }) },
});

const rangeOptions = [
    { value: 'today', label: 'Hôm nay' },
    { value: 'yesterday', label: 'Hôm qua' },
    { value: '7d', label: '7 ngày' },
    { value: '30d', label: '30 ngày' },
    { value: 'this_month', label: 'Tháng này' },
    { value: 'last_month', label: 'Tháng trước' },
    { value: 'custom', label: 'Tùy chọn' },
];

const customFrom = ref(props.filters?.from || '');
const customTo = ref(props.filters?.to || '');

const rangeLabel = computed(() => {
    const opt = rangeOptions.find((o) => o.value === props.filters?.range);
    if (props.filters?.range === 'custom') return `từ ${props.filters.from} đến ${props.filters.to}`;
    return opt ? opt.label.toLowerCase() : '7 ngày';
});

const loadRange = (params) => {
    router.get(route('dashboard'), params, {
        preserveState: true,
        preserveScroll: true,
        only: ['stats', 'filters'],
        replace: true,
    });
};

const selectRange = (value) => {
    if (value === 'custom') {
        loadRange({ range: 'custom', from: customFrom.value, to: customTo.value });
        return;
    }
    loadRange({ range: value });
};

const applyCustom = () => {
    if (!customFrom.value || !customTo.value) return;
    loadRange({ range: 'custom', from: customFrom.value, to: customTo.value });
};

const chartData = computed(() => ({
    labels: (props.stats?.chart || []).map((item) => item.label),
    datasets: [
        {
            label: 'Đơn hàng',
            data: (props.stats?.chart || []).map((item) => item.orders),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.12)',
            fill: true,
            tension: 0.35,
            yAxisID: 'orders',
        },
        {
            label: 'Doanh thu',
            data: (props.stats?.chart || []).map((item) => item.revenue),
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.08)',
            fill: false,
            tension: 0.35,
            yAxisID: 'revenue',
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: { position: 'top', align: 'end' },
        tooltip: {
            callbacks: {
                label: (context) => context.dataset.label === 'Doanh thu'
                    ? ` ${context.dataset.label}: ${Number(context.raw).toLocaleString('vi-VN')}đ`
                    : ` ${context.dataset.label}: ${context.raw}`,
            },
        },
    },
    scales: {
        orders: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Đơn' } },
        revenue: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Doanh thu' } },
    },
};
</script>

<template>
    <Head title="Bảng điều khiển" />

    <AdminLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-gray-800">Bảng điều khiển</h2>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded shadow-sm border-t-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Tổng đơn hàng</h3>
                <p class="text-3xl font-bold mt-2">{{ stats?.total_orders ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow-sm border-t-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Tổng sản phẩm</h3>
                <p class="text-3xl font-bold mt-2">{{ stats?.total_products ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow-sm border-t-4 border-purple-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Tổng doanh thu</h3>
                <p class="text-3xl font-bold mt-2">{{ (stats?.total_revenue ?? 0).toLocaleString('vi-VN') }}đ</p>
            </div>
        </div>

        <div class="mt-8 bg-white p-6 rounded shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Tình hình kinh doanh</h3>
                    <p class="text-sm text-gray-500">Đơn hàng và doanh thu {{ rangeLabel }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-for="opt in rangeOptions"
                        :key="opt.value"
                        type="button"
                        @click="selectRange(opt.value)"
                        class="px-3 py-1.5 text-sm rounded border transition-colors"
                        :class="filters?.range === opt.value
                            ? 'bg-blue-600 border-blue-600 text-white'
                            : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'"
                    >
                        {{ opt.label }}
                    </button>
                </div>
            </div>
            <div v-if="filters?.range === 'custom'" class="flex flex-wrap items-center gap-2 mb-4">
                <input type="date" v-model="customFrom" class="border-gray-300 rounded text-sm py-1.5" />
                <span class="text-gray-500 text-sm">đến</span>
                <input type="date" v-model="customTo" class="border-gray-300 rounded text-sm py-1.5" />
                <button type="button" @click="applyCustom" class="px-3 py-1.5 text-sm rounded bg-blue-600 text-white hover:bg-blue-700">Áp dụng</button>
            </div>
            <div class="h-80">
                <Line :data="chartData" :options="chartOptions" />
            </div>
        </div>

        <div class="mt-8 bg-white p-6 rounded shadow-sm">
            <h3 class="text-lg font-bold mb-4">Chào mừng bạn đến với hệ quản trị Web Đồng Hồ</h3>
            <p class="text-gray-600">Tại đây bạn có thể quản lý sản phẩm, đơn hàng và các thiết lập cho website của mình.</p>
        </div>
    </AdminLayout>
</template>
