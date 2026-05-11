<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';

const props = defineProps({
    wishlist: Array
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const removeFromWishlist = (productId) => {
    router.post(route('wishlist.toggle'), { product_id: productId }, {
        preserveScroll: true
    });
};

const addToCart = (product) => {
    cart.add(product);
};
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
                <div v-for="item in wishlist" :key="item.id" class="group relative">
                    <button 
                        @click="removeFromWishlist(item.product.id)"
                        class="absolute top-4 right-4 z-10 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center shadow hover:bg-[#d10000] hover:text-white transition"
                        title="Bỏ yêu thích"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    
                    <Link :href="route('product.show', item.product.slug)" class="block bg-white p-4 shadow-sm hover:shadow-xl transition border border-gray-100">
                        <div class="relative overflow-hidden aspect-square mb-4 bg-gray-50">
                            <img :src="item.product.image" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
                            <div 
                                v-if="$page.props.settings?.allow_out_of_stock_orders === '1' || item.product.stock > 0"
                                @click.prevent="addToCart(item.product)"
                                class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition duration-300 bg-[#d10000] text-white py-3 text-center text-xs uppercase font-bold tracking-widest"
                            >
                                Thêm vào giỏ
                            </div>
                            <div 
                                v-else
                                class="absolute bottom-0 left-0 right-0 bg-gray-500 text-white py-3 text-center text-xs uppercase font-bold tracking-widest"
                            >
                                Hết hàng
                            </div>
                        </div>
                        <h3 class="text-sm font-bold uppercase tracking-widest mb-2 line-clamp-1 group-hover:text-[#d10000] transition text-center px-2">{{ item.product.name }}</h3>
                        <div class="flex items-center justify-center gap-3">
                            <p class="text-[#d10000] font-bold text-sm">{{ formatPrice(item.product.sale_price || item.product.price) }}</p>
                            <p v-if="item.product.sale_price" class="text-gray-400 text-xs line-through">{{ formatPrice(item.product.price) }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
