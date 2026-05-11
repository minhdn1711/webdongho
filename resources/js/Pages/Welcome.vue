<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';
import { computed, inject } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    categories: Array,
    products: Array,
    latest_posts: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const addToCart = (product) => {
    if (settings.value.allow_out_of_stock_orders !== '1' && product.stock <= 0) {
        return;
    }
    cart.add(product);
};
</script>

<template>
    <Head title="Julius Việt Nam - Đồng Hồ Hàn Quốc Chính Hãng" />

    <ClientLayout>
        <!-- Hero Section -->
        <section class="relative h-[400px] md:h-[600px] bg-gray-200 overflow-hidden">
            <img src="https://julius.vn/wp-content/uploads/2024/01/banner-web-1.jpg" class="w-full h-full object-cover" alt="Hero Banner" />
            <div class="absolute inset-0 bg-black/20 flex flex-col justify-center px-6 md:px-20 text-white">
                <h1 class="text-3xl md:text-6xl font-bold mb-4 italic uppercase">TIME TO SHINE</h1>
                <p class="text-base md:text-xl mb-8 max-w-lg">Khám phá bộ sưu tập đồng hồ Hàn Quốc mới nhất từ Julius.</p>
                <button class="bg-[#d10000] hover:bg-black text-white px-8 py-3 w-max transition font-bold uppercase tracking-widest text-sm">Mua Ngay</button>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="max-w-7xl mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link v-for="cat in categories" :key="cat.id" :href="route('category.show', cat.slug)" class="relative group cursor-pointer overflow-hidden aspect-[4/5]">
                    <img :src="cat.image || 'https://julius.vn/wp-content/uploads/2021/10/banner-nu.jpg'" class="w-full h-full object-cover transition duration-700 group-hover:scale-110" />
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/30 transition flex items-center justify-center">
                        <div class="border-2 border-white px-6 py-2 text-white font-bold tracking-widest uppercase">
                            {{ cat.name }}
                        </div>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold uppercase tracking-widest mb-2 italic text-[#d10000]">Sản Phẩm Nổi Bật</h2>
                    <div class="w-20 h-1 bg-[#d10000] mx-auto"></div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div v-for="product in products" :key="product.id" class="bg-white p-4 group cursor-pointer shadow-sm hover:shadow-xl transition">
                        <div class="relative overflow-hidden aspect-square mb-4">
                            <img :src="product.image" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
                            <div 
                                v-if="settings.allow_out_of_stock_orders === '1' || product.stock > 0"
                                @click.stop="addToCart(product)"
                                class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition duration-300 bg-[#d10000] text-white py-2 text-center text-sm font-bold active:bg-black"
                            >
                                THÊM VÀO GIỎ
                            </div>
                            <div 
                                v-else
                                class="absolute bottom-0 left-0 right-0 bg-gray-500 text-white py-2 text-center text-sm font-bold"
                            >
                                HẾT HÀNG
                            </div>
                        </div>
                        <h3 class="text-sm font-medium h-10 overflow-hidden line-clamp-2 mb-2 group-hover:text-[#d10000] transition">{{ product.name }}</h3>
                        <div class="flex items-center gap-2">
                            <p class="text-[#d10000] font-bold">{{ formatPrice(product.sale_price || product.price) }}</p>
                            <p v-if="product.sale_price" class="text-gray-400 text-xs line-through">{{ formatPrice(product.price) }}</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <button class="border-2 border-black hover:bg-black hover:text-white px-10 py-3 transition font-bold uppercase tracking-widest">Xem Tất Cả</button>
                </div>
            </div>
        </section>

        <!-- Latest News Section -->
        <section class="bg-gray-50 py-16 md:py-24 border-t">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-xs md:text-sm font-bold text-[#d10000] uppercase tracking-[0.4em] mb-4">Kiến thức đồng hồ</h2>
                    <h3 class="text-3xl md:text-5xl font-bold uppercase italic">Tin tức & Sự kiện</h3>
                    <div class="w-20 h-1 bg-[#d10000] mx-auto mt-6"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <Link v-for="post in latest_posts" :key="post.id" :href="route('news.show', post.slug)" class="group bg-white shadow-sm hover:shadow-xl transition">
                        <div class="aspect-[16/9] overflow-hidden">
                            <img :src="post.image || 'https://via.placeholder.com/800x450'" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
                        </div>
                        <div class="p-6">
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-3">{{ formatDate(post.created_at) }}</div>
                            <h4 class="text-lg font-bold mb-4 group-hover:text-[#d10000] transition line-clamp-2 h-14">{{ post.title }}</h4>
                            <span class="text-xs font-bold uppercase tracking-widest text-[#d10000] flex items-center gap-2">
                                Đọc thêm
                                <svg class="w-3 h-3 translate-x-0 group-hover:translate-x-2 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </span>
                        </div>
                    </Link>
                </div>

                <div class="text-center mt-12">
                    <Link :href="route('news.index')" class="inline-block border-2 border-black hover:bg-black hover:text-white px-10 py-3 transition font-bold uppercase tracking-widest text-sm">
                        Xem tất cả bài viết
                    </Link>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>
