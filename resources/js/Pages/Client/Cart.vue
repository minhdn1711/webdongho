<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};
</script>

<template>
    <Head title="Giỏ hàng" />

    <ClientLayout>
        <div class="max-w-7xl mx-auto px-4 py-16">
            <nav class="flex mb-8 text-xs font-bold uppercase tracking-widest text-gray-400">
                <Link :href="'/'" class="hover:text-black">Trang chủ</Link>
                <span class="mx-2">/</span>
                <span class="text-black">Giỏ hàng</span>
            </nav>

            <h1 class="text-3xl font-bold uppercase tracking-widest mb-12 italic text-[#d10000]">Giỏ hàng của bạn</h1>

            <div v-if="cart.items.length === 0" class="text-center py-20 bg-gray-50 border border-dashed">
                <p class="text-gray-500 mb-8 text-lg italic">Giỏ hàng của bạn đang trống.</p>
                <Link :href="'/'" class="inline-block bg-[#d10000] text-white px-10 py-3 uppercase font-bold tracking-widest hover:bg-black transition">
                    Quay lại mua sắm
                </Link>
            </div>

            <div v-else class="flex flex-col lg:flex-row gap-16">
                <!-- Cart Items -->
                <div class="flex-1">
                    <div class="hidden md:grid grid-cols-6 gap-4 pb-4 border-b text-xs font-bold uppercase tracking-widest text-gray-500">
                        <div class="col-span-3">Sản phẩm</div>
                        <div class="text-center">Giá</div>
                        <div class="text-center">Số lượng</div>
                        <div class="text-right">Tổng</div>
                    </div>

                    <div v-for="item in cart.items" :key="item.cartKey ?? item.id" class="grid grid-cols-1 md:grid-cols-6 gap-4 py-8 border-b items-center">
                        <!-- Product Info -->
                        <div class="col-span-3 flex gap-6 items-center">
                            <div class="w-24 h-24 bg-gray-100 shrink-0 border">
                                <img :src="item.image" class="w-full h-full object-cover" />
                            </div>
                            <div>
                                <h3 class="font-bold text-lg mb-1">{{ item.name }}</h3>
                                <div v-if="item.attributes" class="flex flex-wrap gap-1 mb-1">
                                    <span v-for="(val, key) in item.attributes" :key="key" class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                        {{ key }}: {{ val }}
                                    </span>
                                </div>
                                <button @click="cart.remove(item.cartKey ?? item.id)" class="text-xs text-red-500 hover:underline uppercase tracking-tighter font-bold">Xóa sản phẩm</button>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="text-center">
                            <span class="md:hidden text-xs text-gray-400 uppercase font-bold mr-2">Giá:</span>
                            <span class="font-medium">{{ formatPrice(item.price) }}</span>
                        </div>

                        <!-- Quantity -->
                        <div class="flex justify-center">
                            <div class="flex items-center border">
                                <button
                                    @click="cart.updateQuantity(item.cartKey ?? item.id, item.quantity - 1)"
                                    class="px-3 py-1 hover:bg-gray-100"
                                >-</button>
                                <input
                                    type="number"
                                    v-model.number="item.quantity"
                                    @change="cart.updateQuantity(item.cartKey ?? item.id, item.quantity)"
                                    class="w-12 text-center border-none focus:ring-0 text-sm"
                                />
                                <button
                                    @click="cart.updateQuantity(item.cartKey ?? item.id, item.quantity + 1)"
                                    class="px-3 py-1 hover:bg-gray-100"
                                >+</button>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="text-right">
                            <span class="md:hidden text-xs text-gray-400 uppercase font-bold mr-2">Tổng:</span>
                            <span class="font-bold text-[#d10000]">{{ formatPrice(item.price * item.quantity) }}</span>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <Link :href="'/'" class="text-sm font-bold uppercase tracking-widest hover:text-[#d10000] transition flex items-center gap-2">
                            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            Tiếp tục mua hàng
                        </Link>
                        <button @click="cart.clear()" class="text-sm font-bold uppercase tracking-widest text-gray-400 hover:text-black transition">Xóa hết giỏ hàng</button>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="w-full lg:w-96">
                    <div class="bg-gray-50 p-8 sticky top-24">
                        <h2 class="text-xl font-bold uppercase tracking-widest mb-8 border-b pb-4">Tóm tắt đơn hàng</h2>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 uppercase font-bold tracking-tighter">Số lượng sản phẩm</span>
                                <span class="font-bold">{{ cart.count }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 uppercase font-bold tracking-tighter">Tạm tính</span>
                                <span class="font-bold">{{ formatPrice(cart.total) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 uppercase font-bold tracking-tighter">Phí vận chuyển</span>
                                <span class="text-green-600 font-bold uppercase">Miễn phí</span>
                            </div>
                        </div>

                        <div class="border-t border-dashed pt-6 mb-8">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold uppercase tracking-widest">Tổng cộng</span>
                                <span class="text-2xl font-bold text-[#d10000]">{{ formatPrice(cart.total) }}</span>
                            </div>
                        </div>

                        <Link :href="route('checkout.index')" class="block w-full bg-black text-white text-center py-4 uppercase font-bold tracking-widest hover:bg-[#d10000] transition">
                            Tiến hành thanh toán
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
