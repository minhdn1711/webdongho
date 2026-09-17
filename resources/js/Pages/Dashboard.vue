<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
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
    stats: Object
});

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
                    <p class="text-sm text-gray-500">Đơn hàng và doanh thu trong 7 ngày gần nhất</p>
                </div>
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
