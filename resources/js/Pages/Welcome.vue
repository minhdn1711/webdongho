<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    categories: Array,
    products: Array,
    latest_posts: Array,
    banners: Array,
});

const currentBanner = ref(0);
const bannerInterval = ref(null);
const nextBanner = () => {
    if (props.banners && props.banners.length > 0) {
        currentBanner.value = (currentBanner.value + 1) % props.banners.length;
    }
};

onMounted(() => {
    if (props.banners && props.banners.length > 1) {
        bannerInterval.value = setInterval(nextBanner, 5000);
    }
});

onUnmounted(() => {
    if (bannerInterval.value) clearInterval(bannerInterval.value);
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Lamtime Shop - Đồng Hồ Hàn Quốc Chính Hãng" />

    <ClientLayout>
        <!-- Hero Section (Banner Slider) -->
        <section class="relative h-[400px] md:h-[600px] bg-gray-200 overflow-hidden">
            <div v-if="banners && banners.length > 0" class="h-full w-full">
                <div v-for="(banner, index) in banners" :key="banner.id" 
                     v-show="currentBanner === index"
                     class="absolute inset-0 transition-opacity duration-700">
                    <img :src="banner.image_url" class="w-full h-full object-cover" :alt="banner.title" />
                    <div class="absolute inset-0 bg-black/20 flex flex-col justify-center px-6 md:px-20 text-white">
                        <h2 v-if="banner.title" class="text-3xl md:text-6xl font-bold mb-4 italic uppercase animate-fade-in-up">{{ banner.title }}</h2>
                        <a v-if="banner.link" :href="banner.link" class="bg-[#d10000] hover:bg-black text-white px-8 py-3 w-max transition font-bold uppercase tracking-widest text-sm">
                            Mua Ngay
                        </a>
                    </div>
                </div>
                
                <!-- Dots Navigation -->
                <div v-if="banners.length > 1" class="absolute bottom-6 left-0 right-0 flex justify-center gap-2">
                    <button v-for="(_, index) in banners" :key="index"
                            @click="currentBanner = index"
                            :class="['w-2 h-2 rounded-full transition-all', currentBanner === index ? 'bg-white w-6' : 'bg-white/50']">
                    </button>
                </div>
            </div>

            <!-- Default Banner if none exists -->
            <div v-else class="h-full relative">
                <img src="https://julius.vn/wp-content/uploads/2024/01/banner-web-1.jpg" class="w-full h-full object-cover" alt="Hero Banner" />
                <div class="absolute inset-0 bg-black/20 flex flex-col justify-center px-6 md:px-20 text-white">
                    <h1 class="text-3xl md:text-6xl font-bold mb-4 italic uppercase">TIME TO SHINE</h1>
                    <p class="text-base md:text-xl mb-8 max-w-lg">Khám phá bộ sưu tập đồng hồ Hàn Quốc mới nhất từ Julius.</p>
                    <button class="bg-[#d10000] hover:bg-black text-white px-8 py-3 w-max transition font-bold uppercase tracking-widest text-sm">Mua Ngay</button>
                </div>
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
        <section class="bg-white py-16 md:py-24">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center justify-between mb-16 gap-6">
                    <div class="text-center md:text-left">
                        <h2 class="text-xs md:text-sm font-bold text-[#d10000] uppercase tracking-[0.4em] mb-4">Bộ sưu tập mới</h2>
                        <h3 class="text-3xl md:text-5xl font-bold uppercase italic tracking-tight">Sản Phẩm Nổi Bật</h3>
                    </div>
                    <div class="hidden md:block w-40 h-px bg-gray-200"></div>
                    <Link :href="route('category.show')" class="text-xs font-bold uppercase tracking-widest border-b-2 border-[#d10000] pb-1 hover:text-[#d10000] transition">
                        Xem tất cả sản phẩm
                    </Link>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-12">
                    <ProductCard v-for="product in products" :key="product.id" :product="product" />
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="bg-gray-50 py-16 border-y border-gray-100">
            <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                <div class="flex flex-col items-center text-center px-6">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-6 border border-gray-100">
                        <svg class="w-8 h-8 text-[#d10000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <h4 class="text-sm font-bold uppercase tracking-widest mb-3">100% Chính hãng</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Cam kết mọi sản phẩm bán ra đều là hàng chính hãng Julius Hàn Quốc, đầy đủ hộp sổ thẻ.</p>
                </div>
                <div class="flex flex-col items-center text-center px-6 border-x border-gray-200">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-6 border border-gray-100">
                        <svg class="w-8 h-8 text-[#d10000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h4 class="text-sm font-bold uppercase tracking-widest mb-3">Bảo hành 12 tháng</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Chế độ hậu mãi chuyên nghiệp, thay pin miễn phí trọn đời cho mọi đơn hàng tại website.</p>
                </div>
                <div class="flex flex-col items-center text-center px-6">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-6 border border-gray-100">
                        <svg class="w-8 h-8 text-[#d10000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <h4 class="text-sm font-bold uppercase tracking-widest mb-3">Giao hàng tận nơi</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Giao hàng nhanh chóng toàn quốc, thanh toán khi nhận hàng (COD), hỗ trợ kiểm tra hàng.</p>
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

<style scoped>
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}
</style>
