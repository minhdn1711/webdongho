<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';
import { onMounted } from 'vue';

const form = useForm({
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    customer_address: '',
    notes: '',
    items: [],
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const submit = () => {
    form.items = cart.items;
    form.post(route('checkout.store'), {
        onSuccess: () => {
            cart.clear();
        },
    });
};
</script>

<template>
    <Head title="Thanh toán" />

    <ClientLayout>
        <div class="max-w-7xl mx-auto px-4 py-16">
            <h1 class="text-3xl font-bold uppercase tracking-widest mb-12 italic text-[#d10000]">Thanh toán</h1>

            <div v-if="cart.items.length === 0" class="text-center py-20">
                <p class="text-gray-500 mb-8 text-lg">Giỏ hàng của bạn đang trống.</p>
                <Link :href="'/'" class="bg-[#d10000] text-white px-10 py-3 uppercase font-bold tracking-widest hover:bg-black transition">
                    Quay lại mua sắm
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Order Form -->
                <div class="space-y-8">
                    <h2 class="text-xl font-bold border-b pb-4 uppercase tracking-wider">Thông tin giao hàng</h2>
                    
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Họ tên *</label>
                                <input v-model="form.customer_name" type="text" required class="w-full border-gray-300 focus:border-[#d10000] focus:ring-[#d10000] rounded-none px-4 py-3" />
                                <div v-if="form.errors.customer_name" class="text-red-500 text-xs mt-1">{{ form.errors.customer_name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Số điện thoại *</label>
                                <input v-model="form.customer_phone" type="text" required class="w-full border-gray-300 focus:border-[#d10000] focus:ring-[#d10000] rounded-none px-4 py-3" />
                                <div v-if="form.errors.customer_phone" class="text-red-500 text-xs mt-1">{{ form.errors.customer_phone }}</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Địa chỉ email</label>
                            <input v-model="form.customer_email" type="email" class="w-full border-gray-300 focus:border-[#d10000] focus:ring-[#d10000] rounded-none px-4 py-3" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Địa chỉ giao hàng *</label>
                            <textarea v-model="form.customer_address" required rows="3" class="w-full border-gray-300 focus:border-[#d10000] focus:ring-[#d10000] rounded-none px-4 py-3"></textarea>
                            <div v-if="form.errors.customer_address" class="text-red-500 text-xs mt-1">{{ form.errors.customer_address }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Ghi chú đơn hàng</label>
                            <textarea v-model="form.notes" rows="3" class="w-full border-gray-300 focus:border-[#d10000] focus:ring-[#d10000] rounded-none px-4 py-3" placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                        </div>

                        <button type="submit" :disabled="form.processing" class="w-full bg-black text-white py-4 uppercase font-bold tracking-widest hover:bg-[#d10000] transition disabled:opacity-50">
                            {{ form.processing ? 'Đang xử lý...' : 'Đặt hàng ngay' }}
                        </button>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 p-8">
                    <h2 class="text-xl font-bold border-b pb-4 uppercase tracking-wider mb-8">Đơn hàng của bạn</h2>
                    
                    <div class="space-y-6 mb-8 max-h-[400px] overflow-y-auto pr-2">
                        <div v-for="item in cart.items" :key="item.id" class="flex gap-4">
                            <div class="w-20 h-20 bg-white border shrink-0">
                                <img :src="item.image" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold mb-1">{{ item.name }}</h3>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Số lượng: {{ item.quantity }}</span>
                                    <span class="font-bold text-[#d10000]">{{ formatPrice(item.price * item.quantity) }}</span>
                                </div>
                                <button @click="cart.remove(item.id)" class="text-xs text-gray-400 hover:text-red-500 mt-2 underline">Xóa</button>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-6 space-y-4 font-bold">
                        <div class="flex justify-between text-gray-600">
                            <span>Tạm tính</span>
                            <span>{{ formatPrice(cart.total) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Giao hàng</span>
                            <span>Miễn phí</span>
                        </div>
                        <div class="flex justify-between text-xl text-[#d10000] pt-4 border-t border-dashed">
                            <span>Tổng cộng</span>
                            <span>{{ formatPrice(cart.total) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
