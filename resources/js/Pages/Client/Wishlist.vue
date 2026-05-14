<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    wishlist: Array
});
</script>

<template>
    <Head title="Sản phẩm yêu thích - Julius Việt Nam" />

    <ClientLayout>
        <div class="max-w-7xl mx-auto px-4 py-16">
            <h1 class="text-3xl font-bold uppercase italic tracking-widest text-center mb-4">Sản phẩm yêu thích</h1>
            <div class="w-16 h-1 bg-[#d10000] mx-auto mb-12"></div>

            <div v-if="wishlist.length === 0" class="text-center py-20 bg-gray-50 border border-dashed border-gray-200">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                </div>
                <h2 class="text-xl font-bold mb-4">Danh sách yêu thích trống</h2>
                <p class="text-gray-500 mb-8">Bạn chưa lưu sản phẩm nào vào danh sách yêu thích.</p>
                <Link href="/" class="bg-black text-white px-8 py-3 uppercase font-bold tracking-widest text-sm hover:bg-[#d10000] transition">
                    Tiếp tục mua sắm
                </Link>
            </div>

            <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <ProductCard v-for="item in wishlist" :key="item.id" :product="item.product" />
            </div>
        </div>
    </ClientLayout>
</template>
