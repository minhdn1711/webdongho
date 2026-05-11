<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};
</script>

<template>
    <Head title="Đặt hàng thành công" />

    <ClientLayout>
        <div class="max-w-3xl mx-auto px-4 py-20 text-center">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>

            <h1 class="text-4xl font-bold mb-4 italic text-[#d10000]">Cảm ơn bạn đã đặt hàng!</h1>
            <p class="text-lg text-gray-600 mb-12">Mã đơn hàng của bạn là: <span class="font-bold text-black">{{ order.order_number }}</span></p>

            <div class="bg-gray-50 p-8 rounded-none text-left mb-12">
                <h2 class="text-lg font-bold border-b pb-4 mb-6 uppercase tracking-wider">Chi tiết giao hàng</h2>
                <div class="space-y-4 text-sm">
                    <div class="flex">
                        <span class="w-32 text-gray-500 font-bold uppercase">Người nhận:</span>
                        <span class="font-bold">{{ order.customer_name }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-500 font-bold uppercase">Điện thoại:</span>
                        <span class="font-bold">{{ order.customer_phone }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-500 font-bold uppercase">Địa chỉ:</span>
                        <span class="font-bold">{{ order.customer_address }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-500 font-bold uppercase">Tổng tiền:</span>
                        <span class="font-bold text-[#d10000]">{{ formatPrice(order.total_amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <Link :href="'/'" class="bg-black text-white px-10 py-3 uppercase font-bold tracking-widest hover:bg-[#d10000] transition">
                    Tiếp tục mua sắm
                </Link>
                <button class="border-2 border-black px-10 py-3 uppercase font-bold tracking-widest hover:bg-black hover:text-white transition">
                    In đơn hàng
                </button>
            </div>
        </div>
    </ClientLayout>
</template>
