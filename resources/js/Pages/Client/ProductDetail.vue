<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { cart } from '@/Services/CartService';
import ProductCard from '@/Components/ProductCard.vue';
import { ref, computed, reactive } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    product: Object,
    relatedProducts: Array,
    isWishlisted: Boolean,
    averageRating: Number,
    reviewCount: Number,
});

// Build full gallery: main image first, then extra images
const allImages = computed(() => {
    const imgs = [];
    if (props.product.image) imgs.push(props.product.image);
    if (props.product.product_images) {
        for (const pi of props.product.product_images) {
            imgs.push(pi.image_url);
        }
    }
    return imgs;
});

const activeImage = ref(allImages.value[0] || '');
const lightboxOpen = ref(false);

const setActiveImage = (url) => {
    activeImage.value = url;
};

const openLightbox = () => {
    lightboxOpen.value = true;
};

const closeLightbox = () => {
    lightboxOpen.value = false;
};

const prevImage = () => {
    const idx = allImages.value.indexOf(activeImage.value);
    activeImage.value = allImages.value[(idx - 1 + allImages.value.length) % allImages.value.length];
};

const nextImage = () => {
    const idx = allImages.value.indexOf(activeImage.value);
    activeImage.value = allImages.value[(idx + 1) % allImages.value.length];
};

const quantity = ref(1);
const activeTab = ref('description');

// Group product_attribute_values by attribute
const groupedAttributes = computed(() => {
    const groups = {};
    for (const pav of props.product.product_attribute_values || []) {
        const id = pav.attribute_id;
        if (!groups[id]) groups[id] = { attribute: pav.attribute, values: [] };
        groups[id].values.push(pav.attribute_value);
    }
    return Object.values(groups);
});

const selectedAttributes = reactive({}); // { "Màu sắc": "Đỏ", ... }

const selectAttribute = (attrName, valueName) => {
    selectedAttributes[attrName] = valueName;
};

const allSelected = computed(() =>
    groupedAttributes.value.every(g => selectedAttributes[g.attribute.name])
);

const form = useForm({
    product_id: props.product.id,
    rating: 5,
    comment: '',
    customer_name: '',
    images: [],
});

const imagePreviews = ref([]);

const handleReviewImages = (e) => {
    const files = Array.from(e.target.files);
    if (form.images.length + files.length > 5) {
        alert('Tối đa 5 ảnh');
        return;
    }
    form.images = [...form.images, ...files].slice(0, 5);
    imagePreviews.value = form.images.map(f => URL.createObjectURL(f));
};

const removeReviewImage = (index) => {
    form.images.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

const submitReview = () => {
    form.post(route('reviews.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('comment', 'rating', 'images');
            imagePreviews.value = [];
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
    const attrs = Object.keys(selectedAttributes).length > 0 ? { ...selectedAttributes } : null;
    cart.add(props.product, quantity.value, attrs);
};
</script>

<template>
    <Head :title="product.name + ' - Lamtime Shop'" />

    <ClientLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 md:py-16">
            <!-- Breadcrumbs -->
            <nav class="flex mb-8 text-xs font-bold uppercase tracking-widest text-gray-400">
                <Link href="/" class="hover:text-black">Trang chủ</Link>
                <span class="mx-2">/</span>
                <Link v-if="product.categories && product.categories.length > 0" :href="route('category.show', product.categories[0].slug)" class="hover:text-black">{{ product.categories[0].name }}</Link>
                <span class="mx-2">/</span>
                <span class="text-gray-900">{{ product.name }}</span>
            </nav>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20">
                <!-- ===== Product Gallery (Flatsome style) ===== -->
                <div>
                    <!-- Main image -->
                    <div
                        class="relative aspect-square bg-gray-50 overflow-hidden border border-gray-100 mb-3 cursor-zoom-in group"
                        @click="openLightbox"
                    >
                        <img
                            :src="activeImage"
                            :key="activeImage"
                            class="w-full h-full object-cover transition-opacity duration-300"
                            :alt="product.name"
                        />
                        <!-- Zoom icon -->
                        <div class="absolute bottom-3 right-3 bg-white/80 rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                        </div>
                        <!-- Prev/Next arrows (only when multiple images) -->
                        <template v-if="allImages.length > 1">
                            <button
                                @click.stop="prevImage"
                                class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-1.5 shadow transition opacity-0 group-hover:opacity-100"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button
                                @click.stop="nextImage"
                                class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-1.5 shadow transition opacity-0 group-hover:opacity-100"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </template>
                    </div>

                    <!-- Thumbnails strip -->
                    <div v-if="allImages.length > 1" class="flex gap-2 overflow-x-auto pb-1">
                        <button
                            v-for="(img, idx) in allImages"
                            :key="idx"
                            @click="setActiveImage(img)"
                            class="flex-shrink-0 w-16 h-16 border-2 rounded overflow-hidden transition"
                            :class="activeImage === img ? 'border-[#d10000]' : 'border-gray-200 hover:border-gray-400'"
                        >
                            <img :src="img" class="w-full h-full object-cover" :alt="`Ảnh ${idx + 1}`" />
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="flex flex-col">
                    <div class="mb-6">
                        <h1 class="text-3xl md:text-4xl font-bold mb-4 italic uppercase tracking-tight">{{ product.name }}</h1>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center text-yellow-400">
                                <template v-for="i in 5" :key="i">
                                    <svg class="w-4 h-4" :class="i <= Math.round(averageRating) ? 'text-yellow-400 fill-current' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                </template>
                            </div>
                            <span class="text-sm text-gray-500 font-medium">({{ reviewCount }} đánh giá)</span>
                        </div>

                        <div class="flex items-baseline gap-4 mb-6">
                            <span class="text-3xl font-bold text-[#d10000]">{{ formatPrice(product.sale_price || product.price) }}</span>
                            <span v-if="product.sale_price" class="text-xl text-gray-400 line-through">{{ formatPrice(product.price) }}</span>
                        </div>

                        <div v-if="product.short_description" class="text-gray-500 text-sm mb-4 leading-relaxed border-l-2 border-[#d10000] pl-4 italic">
                            {{ product.short_description }}
                        </div>
                    </div>

                    <!-- Attributes Selector -->
                    <div v-if="groupedAttributes.length > 0" class="space-y-4 mb-6">
                        <div v-for="group in groupedAttributes" :key="group.attribute.id">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ group.attribute.name }}:</span>
                                <span v-if="selectedAttributes[group.attribute.name]" class="text-xs font-semibold text-gray-800">{{ selectedAttributes[group.attribute.name] }}</span>
                            </div>

                            <!-- Color type -->
                            <div v-if="group.attribute.type === 'color'" class="flex flex-wrap gap-2">
                                <button
                                    v-for="val in group.values"
                                    :key="val.id"
                                    @click="selectAttribute(group.attribute.name, val.value)"
                                    :title="val.value"
                                    class="w-8 h-8 rounded-full border-2 transition-all"
                                    :style="{ backgroundColor: val.meta_value }"
                                    :class="selectedAttributes[group.attribute.name] === val.value
                                        ? 'border-black scale-110 ring-2 ring-black ring-offset-1'
                                        : 'border-gray-300 hover:border-gray-500'"
                                />
                            </div>

                            <!-- Button / Text type -->
                            <div v-else class="flex flex-wrap gap-2">
                                <button
                                    v-for="val in group.values"
                                    :key="val.id"
                                    @click="selectAttribute(group.attribute.name, val.value)"
                                    class="px-4 py-1.5 border text-sm font-medium transition-all"
                                    :class="selectedAttributes[group.attribute.name] === val.value
                                        ? 'border-black bg-black text-white'
                                        : 'border-gray-300 text-gray-700 hover:border-black'"
                                >
                                    {{ val.value }}
                                </button>
                            </div>
                        </div>

                        <!-- Warning nếu chưa chọn đủ -->
                        <p v-if="!allSelected" class="text-xs text-amber-600 font-medium">
                            Vui lòng chọn đầy đủ các thuộc tính trước khi thêm vào giỏ.
                        </p>
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
                                :disabled="groupedAttributes.length > 0 && !allSelected"
                                class="flex-1 bg-[#d10000] hover:bg-black text-white px-8 py-3 uppercase font-bold tracking-[0.2em] transition duration-300 disabled:opacity-40 disabled:cursor-not-allowed"
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
                                <Link v-if="product.categories && product.categories.length > 0" :href="route('category.show', product.categories[0].slug)" class="hover:text-[#d10000]">{{ product.categories[0].name }}</Link>
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
                        <p>Lamtime là thương hiệu đồng hồ nổi tiếng đến từ Hàn Quốc, được yêu thích bởi các thiết kế thời trang, đa dạng và giá thành hợp lý. Mỗi chiếc đồng hồ Lamtime đều được chế tác tỉ mỉ, sử dụng bộ máy bền bỉ và chất liệu an toàn.</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Chất liệu mặt kính: Mineral Crystal (kính khoáng) chống va đập tốt.</li>
                            <li>Chất liệu dây: Dây da cao cấp hoặc thép không gỉ.</li>
                            <li>Độ chống nước: 3ATM (30m) - Rửa tay, đi mưa nhỏ.</li>
                            <li>Bảo hành: 12 tháng chính hãng.</li>
                        </ul>
                    </div>
                </div>

                <div v-if="activeTab === 'reviews'" class="py-12 grid grid-cols-1 md:grid-cols-2 gap-12">
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
                                <div v-if="review.images && review.images.length" class="pl-14 mt-3 flex gap-2 flex-wrap">
                                    <img v-for="(img, idx) in review.images" :key="idx" :src="img" class="w-16 h-16 object-cover rounded border">
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 italic text-sm">Chưa có đánh giá nào cho sản phẩm này.</div>
                    </div>

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
                                    <button v-for="i in 5" :key="i" type="button" @click="form.rating = i"
                                        class="text-2xl transition hover:scale-110"
                                        :class="i <= form.rating ? 'text-yellow-400' : 'text-gray-300'">★</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Nhận xét</label>
                                <textarea v-model="form.comment" required rows="4" class="w-full border-gray-300 focus:border-[#d10000] focus:ring-0 text-sm resize-none"></textarea>
                                <div v-if="form.errors.comment" class="text-red-500 text-xs mt-1">{{ form.errors.comment }}</div>
                            </div>

                            <!-- Upload ảnh đánh giá -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Ảnh minh họa (tối đa 5)</label>
                                <div class="flex flex-wrap gap-3 mb-3">
                                    <div v-for="(preview, index) in imagePreviews" :key="index" class="relative w-20 h-20 border rounded overflow-hidden">
                                        <img :src="preview" class="w-full h-full object-cover">
                                        <button type="button" @click="removeReviewImage(index)"
                                            class="absolute top-0 right-0 bg-red-500 text-white w-5 h-5 flex items-center justify-center text-xs">×</button>
                                    </div>
                                    <label v-if="imagePreviews.length < 5" class="w-20 h-20 border-2 border-dashed flex items-center justify-center text-gray-400 cursor-pointer hover:border-[#d10000]">
                                        <span class="text-3xl">+</span>
                                        <input type="file" multiple accept="image/*" class="hidden" @change="handleReviewImages">
                                    </label>
                                </div>
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
                    <ProductCard v-for="item in relatedProducts" :key="item.id" :product="item" />
                </div>
            </div>
        </div>

        <!-- Lightbox -->
        <Teleport to="body">
            <div
                v-if="lightboxOpen"
                class="fixed inset-0 z-[200] bg-black/90 flex items-center justify-center"
                @click.self="closeLightbox"
                @keydown.esc="closeLightbox"
            >
                <button @click="closeLightbox" class="absolute top-4 right-4 text-white/70 hover:text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <template v-if="allImages.length > 1">
                    <button @click="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button @click="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </template>

                <img
                    :src="activeImage"
                    class="max-w-[90vw] max-h-[90vh] object-contain"
                    :alt="product.name"
                />

                <!-- Thumbnail strip in lightbox -->
                <div v-if="allImages.length > 1" class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 px-4">
                    <button
                        v-for="(img, idx) in allImages"
                        :key="idx"
                        @click="setActiveImage(img)"
                        class="w-12 h-12 border-2 rounded overflow-hidden flex-shrink-0 transition"
                        :class="activeImage === img ? 'border-white' : 'border-white/30 hover:border-white/70'"
                    >
                        <img :src="img" class="w-full h-full object-cover" />
                    </button>
                </div>
            </div>
        </Teleport>
    </ClientLayout>
</template>
