<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';
import { ref, computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    product: Object,
    relatedProducts: Array,
    isWishlisted: Boolean,
    averageRating: Number,
    reviewCount: Number,
});

const quantity = ref(1);
const activeTab = ref('description');

const form = useForm({
    product_id: props.product.id,
    rating: 5,
    comment: '',
    customer_name: ''
});

const submitReview = () => {
    form.post(route('reviews.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('comment', 'rating');
            alert('Cảm ơn bạn đã đánh giá!');
        }
    });
};

const toggleWishlist = () => {
    if (!user.value) {
        router.visit(route('login'));
        return;
    }
    router.post(route('wishlist.toggle'), { product_id: props.product.id }, {
        preserveScroll: true
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const addToCart = () => {
    for (let i = 0; i < quantity.value; i++) {
        cart.add(props.product);
    }
};
</script>

<template>
    <Head :title="product.name + ' - Julius Việt Nam'" />

    <ClientLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 md:py-16">
            <!-- Breadcrumbs -->
            <nav class="flex mb-8 text-xs font-bold uppercase tracking-widest text-gray-400">
                <Link href="/" class="hover:text-black">Trang chủ</Link>
                <span class="mx-2">/</span>
                <Link v-if="product.category" :href="route('category.show', product.category.slug)" class="hover:text-black">{{ product.category.name }}</Link>
                <span class="mx-2">/</span>
                <span class="text-gray-900">{{ product.name }}</span>
            </nav>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20">
                <!-- Product Gallery -->
                <div class="space-y-4">
                    <div class="aspect-square bg-gray-50 overflow-hidden border border-gray-100 group">
                        <img :src="product.image" class="w-full h-full object-cover transition duration-700 group-hover:scale-110" :alt="product.name" />
                    </div>
                </div>

                <!-- Product Info -->
                <div class="flex flex-col">
                    <div class="mb-6">
                        <h1 class="text-3xl md:text-4xl font-bold mb-4 italic uppercase tracking-tight">{{ product.name }}</h1>
                        
                        <div v-if="product.short_description" class="text-gray-500 text-sm mb-4 leading-relaxed border-l-2 border-[#d10000] pl-4 italic">
                            {{ product.short_description }}
                        </div>
                        
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center text-yellow-400">
                                <template v-for="i in 5" :key="i">
                                    <svg class="w-4 h-4" :class="i <= Math.round(averageRating) ? 'text-yellow-400 fill-current' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                </template>
                            </div>
                            <span class="text-sm text-gray-500 font-medium">({{ reviewCount }} đánh giá)</span>
                        </div>

                        <div class="w-16 h-1 bg-[#d10000] mb-6"></div>
                        
                        <div class="flex items-baseline gap-4 mb-6">
                            <span class="text-3xl font-bold text-[#d10000]">{{ formatPrice(product.sale_price || product.price) }}</span>
                            <span v-if="product.sale_price" class="text-xl text-gray-400 line-through">{{ formatPrice(product.price) }}</span>
                        </div>

                        <div class="prose prose-sm max-w-none text-gray-600 mb-8 leading-relaxed">
                            <p v-if="product.description" v-html="product.description"></p>
                            <p v-else>Đồng hồ Julius chính hãng từ Hàn Quốc. Thiết kế trẻ trung, thời thượng với chất lượng vượt trội. Phù hợp cho mọi dịp từ công sở đến dạo phố.</p>
                        </div>
                    </div>

                    <!-- Add to Cart -->
                    <div class="flex flex-col gap-6 pt-6 border-t border-gray-100">
                        <div v-if="product.stock > 0" class="flex flex-wrap gap-4 items-center">
                            <div class="flex items-center border border-gray-300">
                                <button @click="quantity > 1 && quantity--" class="px-4 py-2 hover:bg-gray-100 transition">-</button>
                                <input v-model="quantity" type="number" class="w-16 text-center border-none focus:ring-0 font-bold" min="1" />
                                <button @click="quantity++" class="px-4 py-2 hover:bg-gray-100 transition">+</button>
                            </div>
                            <button 
                                @click="addToCart"
                                class="flex-1 bg-[#d10000] hover:bg-black text-white px-8 py-3 uppercase font-bold tracking-[0.2em] transition duration-300"
                            >
                                Thêm vào giỏ hàng
                            </button>
                            <button 
                                @click="toggleWishlist"
                                class="w-12 h-12 flex items-center justify-center border transition duration-300"
                                :class="isWishlisted ? 'bg-black border-black text-white' : 'border-gray-300 text-gray-400 hover:border-black hover:text-black'"
                                title="Yêu thích"
                            >
                                <svg class="w-5 h-5" :class="{'fill-current': isWishlisted}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                            </button>
                        </div>
                        <div v-else class="text-center py-3 bg-gray-100 text-gray-500 font-bold uppercase tracking-widest border border-gray-200">
                            Sản phẩm hiện đang hết hàng
                        </div>

                        <div class="space-y-4 text-xs font-bold uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">Danh mục:</span>
                                <Link v-if="product.category" :href="route('category.show', product.category.slug)" class="hover:text-[#d10000]">{{ product.category.name }}</Link>
                                <span v-else>Đang cập nhật</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">Tình trạng:</span>
                                <span :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ product.stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-12 grid grid-cols-2 gap-4 border-t pt-8 border-dashed">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 shrink-0">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest leading-tight">Cam kết chính hãng 100%</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 shrink-0">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest leading-tight">Giao hàng miễn phí toàn quốc</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="mt-20 border-t">
                <div class="flex gap-8 border-b">
                    <button 
                        @click="activeTab = 'description'"
                        class="py-4 border-b-2 font-bold uppercase tracking-widest text-sm transition"
                        :class="activeTab === 'description' ? 'border-[#d10000] text-[#d10000]' : 'border-transparent text-gray-400 hover:text-black'"
                    >
                        Mô tả sản phẩm
                    </button>
                    <button 
                        @click="activeTab = 'reviews'"
                        class="py-4 border-b-2 font-bold uppercase tracking-widest text-sm transition flex items-center gap-2"
                        :class="activeTab === 'reviews' ? 'border-[#d10000] text-[#d10000]' : 'border-transparent text-gray-400 hover:text-black'"
                    >
                        Đánh giá <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-0.5 rounded-full">{{ reviewCount }}</span>
                    </button>
                </div>
                
                <div v-if="activeTab === 'description'" class="py-12 prose max-w-none">
                    <p v-if="product.description" v-html="product.description"></p>
                    <div v-else class="space-y-6">
                        <p>Julius là thương hiệu đồng hồ nổi tiếng đến từ Hàn Quốc, được yêu thích bởi các thiết kế thời trang, đa dạng và giá thành hợp lý. Mỗi chiếc đồng hồ Julius đều được chế tác tỉ mỉ, sử dụng bộ máy bền bỉ và chất liệu an toàn.</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Chất liệu mặt kính: Mineral Crystal (kính khoáng) chống va đập tốt.</li>
                            <li>Chất liệu dây: Dây da cao cấp hoặc thép không gỉ.</li>
                            <li>Độ chống nước: 3ATM (30m) - Rửa tay, đi mưa nhỏ.</li>
                            <li>Bảo hành: 12 tháng chính hãng.</li>
                        </ul>
                    </div>
                </div>

                <div v-if="activeTab === 'reviews'" class="py-12 grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- List Reviews -->
                    <div>
                        <h3 class="text-xl font-bold uppercase italic tracking-widest mb-8">Đánh giá từ khách hàng</h3>
                        
                        <div v-if="product.reviews && product.reviews.length > 0" class="space-y-8">
                            <div v-for="review in product.reviews" :key="review.id" class="border-b border-gray-100 pb-8 last:border-0">
                                <div class="flex items-center gap-4 mb-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center font-bold text-gray-400 uppercase">
                                        {{ review.customer_name ? review.customer_name.charAt(0) : 'K' }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm">{{ review.customer_name || 'Khách hàng' }}</div>
                                        <div class="flex text-yellow-400 text-xs mt-1">
                                            <template v-for="i in 5" :key="i">
                                                <svg class="w-3 h-3" :class="i <= review.rating ? 'fill-current' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 pl-14">{{ review.comment }}</p>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 italic text-sm">Chưa có đánh giá nào cho sản phẩm này.</div>
                    </div>

                    <!-- Review Form -->
                    <div class="bg-gray-50 p-8">
                        <h3 class="text-lg font-bold uppercase tracking-widest mb-6">Viết đánh giá</h3>
                        <form @submit.prevent="submitReview" class="space-y-4">
                            <div v-if="!user">
                                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Tên của bạn</label>
                                <input v-model="form.customer_name" type="text" required class="w-full border-gray-300 focus:border-[#d10000] focus:ring-0 text-sm" />
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Đánh giá của bạn</label>
                                <div class="flex gap-2">
                                    <button 
                                        v-for="i in 5" :key="i" 
                                        type="button"
                                        @click="form.rating = i"
                                        class="text-2xl transition hover:scale-110"
                                        :class="i <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
                                    >
                                        ★
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Nhận xét</label>
                                <textarea v-model="form.comment" required rows="4" class="w-full border-gray-300 focus:border-[#d10000] focus:ring-0 text-sm resize-none"></textarea>
                                <div v-if="form.errors.comment" class="text-red-500 text-xs mt-1">{{ form.errors.comment }}</div>
                            </div>

                            <button type="submit" :disabled="form.processing" class="bg-black text-white px-8 py-3 uppercase font-bold tracking-widest text-sm hover:bg-[#d10000] transition w-full disabled:opacity-50">
                                {{ form.processing ? 'Đang gửi...' : 'Gửi đánh giá' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div v-if="relatedProducts.length > 0" class="mt-20">
                <div class="flex items-center justify-between mb-10">
                    <h2 class="text-2xl font-bold uppercase italic tracking-widest">Sản phẩm liên quan</h2>
                    <div class="flex-1 h-px bg-gray-100 mx-8 hidden md:block"></div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <Link 
                        v-for="item in relatedProducts" 
                        :key="item.id" 
                        :href="route('product.show', item.slug)"
                        class="group"
                    >
                        <div class="aspect-square overflow-hidden bg-gray-50 border border-gray-100 mb-4">
                            <img :src="item.image" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
                        </div>
                        <h3 class="text-sm font-bold uppercase tracking-tight line-clamp-1 group-hover:text-[#d10000] transition mb-2">{{ item.name }}</h3>
                        <p class="text-[#d10000] font-bold">{{ formatPrice(item.sale_price || item.price) }}</p>
                    </Link>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
