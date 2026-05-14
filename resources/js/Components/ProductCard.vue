<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';
import { computed } from 'vue';

const props = defineProps({
    product: Object,
});

const page = usePage();
const settings = computed(() => page.props.settings || {});
const auth = computed(() => page.props.auth || {});

const isInWishlist = computed(() => {
    return auth.value.wishlist_ids?.includes(props.product.id) || false;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const addToCart = (product) => {
    if (settings.value.allow_out_of_stock_orders !== '1' && product.stock <= 0) {
        return;
    }
    cart.add(product);
};

const toggleWishlist = () => {
    if (!auth.value.user) {
        router.get(route('login'));
        return;
    }
    
    router.post(route('wishlist.toggle'), { product_id: props.product.id }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="group relative bg-white flex flex-col h-full border border-gray-100 hover:shadow-xl transition-all duration-300">
        <!-- Wishlist Button -->
        <button 
            @click.stop="toggleWishlist"
            class="absolute top-3 right-3 z-30 w-9 h-9 rounded-full bg-white shadow-md flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-90"
            :class="isInWishlist ? 'text-red-500' : 'text-gray-300 hover:text-red-500'"
            title="Thêm vào yêu thích"
        >
            <svg 
                class="w-5 h-5 transition-transform" 
                :fill="isInWishlist ? 'currentColor' : 'none'" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>

        <Link :href="route('product.show', product.slug)" class="flex-1 flex flex-col">
            <!-- Product Image -->
            <div class="relative overflow-hidden aspect-[4/5] bg-gray-50 border-b border-gray-50">
                <img 
                    :src="product.image" 
                    :alt="product.name"
                    class="w-full h-full object-cover transition duration-700 group-hover:scale-110" 
                />
                
                <!-- Sale Badge -->
                <div v-if="product.sale_price" class="absolute top-4 left-4 bg-[#d10000] text-white text-[10px] font-bold px-3 py-1 uppercase tracking-widest shadow-sm">
                    Sale
                </div>

                <!-- Add to Cart Overlay (Desktop) -->
                <div 
                    v-if="settings.allow_out_of_stock_orders === '1' || product.stock > 0"
                    @click.prevent="addToCart(product)"
                    class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition duration-300 bg-black/90 text-white py-4 text-center text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-[#d10000] cursor-pointer"
                >
                    Thêm vào giỏ hàng
                </div>
                <div 
                    v-else
                    class="absolute bottom-0 left-0 right-0 bg-gray-500/90 text-white py-4 text-center text-[10px] font-bold uppercase tracking-[0.2em]"
                >
                    Hết hàng
                </div>
            </div>

            <!-- Product Info -->
            <div class="p-4 text-center flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest mb-2 line-clamp-2 group-hover:text-[#d10000] transition px-2 min-h-[2rem]">
                        {{ product.name }}
                    </h3>
                    <div class="flex items-center justify-center gap-3">
                        <p class="text-[#d10000] font-bold text-sm">{{ formatPrice(product.sale_price || product.price) }}</p>
                        <p v-if="product.sale_price" class="text-gray-400 text-[10px] line-through">{{ formatPrice(product.price) }}</p>
                    </div>
                </div>
            </div>
        </Link>
    </div>
</template>
