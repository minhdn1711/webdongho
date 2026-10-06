<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';
import CategoryTreeItem from '@/Components/CategoryTreeItem.vue';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    category: Object,
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const minPrice = ref(props.filters.min_price || '');
const maxPrice = ref(props.filters.max_price || '');
const sort = ref(props.filters.sort || 'newest');
const showFilters = ref(false);

const filter = () => {
    router.get(route('category.show', props.category?.slug), {
        search: search.value,
        min_price: minPrice.value,
        max_price: maxPrice.value,
        sort: sort.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch([search, minPrice, maxPrice, sort], debounce(filter, 500));

const sortOptions = [
    { value: 'newest', label: 'Mới nhất' },
    { value: 'price_asc', label: 'Giá: Thấp đến Cao' },
    { value: 'price_desc', label: 'Giá: Cao đến Thấp' },
    { value: 'name_asc', label: 'Tên: A-Z' },
];

const pricePresets = [
    { label: 'Dưới 1 triệu', min: '', max: 1000000 },
    { label: '1 – 3 triệu', min: 1000000, max: 3000000 },
    { label: '3 – 5 triệu', min: 3000000, max: 5000000 },
    { label: 'Trên 5 triệu', min: 5000000, max: '' },
];

const isPreset = (preset) => String(minPrice.value) === String(preset.min) && String(maxPrice.value) === String(preset.max);

const togglePreset = (preset) => {
    if (isPreset(preset)) {
        minPrice.value = '';
        maxPrice.value = '';
    } else {
        minPrice.value = preset.min;
        maxPrice.value = preset.max;
    }
};

const formatPrice = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

// Active filter chips
const activeFilters = computed(() => {
    const chips = [];
    if (search.value) chips.push({ key: 'search', label: `"${search.value}"`, clear: () => (search.value = '') });
    if (minPrice.value !== '' || maxPrice.value !== '') {
        const from = minPrice.value !== '' ? formatPrice(minPrice.value) : '0đ';
        const to = maxPrice.value !== '' ? formatPrice(maxPrice.value) : 'trở lên';
        chips.push({ key: 'price', label: `${from} – ${to}`, clear: () => { minPrice.value = ''; maxPrice.value = ''; } });
    }
    return chips;
});

const clearAll = () => {
    search.value = '';
    minPrice.value = '';
    maxPrice.value = '';
};

// Sub-categories of the current category (from the already-shared tree)
const findNode = (nodes, id) => {
    for (const node of nodes || []) {
        if (node.id === id) return node;
        const found = findNode(node.children, id);
        if (found) return found;
    }
    return null;
};

const subCategories = computed(() => {
    if (!props.category) return props.categories || [];
    return findNode(props.categories, props.category.id)?.children || [];
});

const pageLabel = (link, index) => {
    const last = props.products.links.length - 1;
    if (index === 0) return '&laquo; Trước';
    if (index === last) return 'Sau &raquo;';
    return link.label;
};
</script>

<template>
    <Head :title="category ? category.name : 'Tất cả sản phẩm'" />

    <ClientLayout>
        <!-- Hero -->
        <section class="relative bg-black text-white overflow-hidden">
            <img
                v-if="category?.image"
                :src="category.image"
                :alt="category.name"
                class="absolute inset-0 w-full h-full object-cover opacity-40"
            />
            <div v-else class="absolute inset-0 bg-gradient-to-br from-neutral-900 via-black to-[#3a0000]"></div>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-[#d10000]"></div>

            <div class="relative max-w-7xl mx-auto px-4 py-12 md:py-16">
                <nav class="flex items-center mb-5 text-[11px] font-bold uppercase tracking-widest text-white/60">
                    <Link :href="'/'" class="hover:text-white transition">Trang chủ</Link>
                    <span class="mx-2">/</span>
                    <Link v-if="category" :href="route('category.show')" class="hover:text-white transition">Sản phẩm</Link>
                    <span v-if="category" class="mx-2">/</span>
                    <span class="text-white">{{ category ? category.name : 'Tất cả sản phẩm' }}</span>
                </nav>
                <h1 class="font-display text-3xl md:text-5xl font-bold uppercase tracking-wide">
                    {{ category ? category.name : 'Tất cả sản phẩm' }}
                </h1>
                <p v-if="category?.description" class="mt-4 max-w-2xl text-sm md:text-base text-white/70 leading-relaxed">
                    {{ category.description }}
                </p>

                <!-- Sub-category chips -->
                <div v-if="subCategories.length" class="mt-7 flex flex-wrap gap-2">
                    <Link
                        v-for="sub in subCategories"
                        :key="sub.id"
                        :href="route('category.show', sub.slug)"
                        class="px-4 py-2 text-[11px] font-bold uppercase tracking-widest border border-white/30 hover:bg-[#d10000] hover:border-[#d10000] transition"
                    >{{ sub.name }}</Link>
                </div>
            </div>
        </section>

        <div class="bg-gray-50 py-8 md:py-10">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
                    <!-- Sidebar -->
                    <aside class="w-full lg:w-64 shrink-0">
                        <button
                            @click="showFilters = !showFilters"
                            class="lg:hidden w-full flex items-center justify-between bg-white border border-gray-200 px-4 py-3 text-xs font-bold uppercase tracking-widest"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4" /></svg>
                                Bộ lọc
                                <span v-if="activeFilters.length" class="bg-[#d10000] text-white rounded-full w-5 h-5 text-[10px] flex items-center justify-center">{{ activeFilters.length }}</span>
                            </span>
                            <svg class="w-4 h-4 transition-transform" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <div :class="showFilters ? 'block' : 'hidden'" class="lg:block mt-3 lg:mt-0 lg:sticky lg:top-24 space-y-5">
                            <!-- Search -->
                            <div class="bg-white border border-gray-100 p-5">
                                <h3 class="text-xs font-bold uppercase tracking-widest mb-3">Tìm kiếm</h3>
                                <div class="relative">
                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Tên sản phẩm..."
                                        class="w-full border-gray-200 focus:border-[#d10000] focus:ring-[#d10000] text-sm pl-9 pr-3 py-2"
                                    />
                                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                            </div>

                            <!-- Categories -->
                            <div class="bg-white border border-gray-100 p-5">
                                <h3 class="text-xs font-bold uppercase tracking-widest mb-3">Danh mục</h3>
                                <div class="text-sm space-y-2">
                                    <Link
                                        :href="route('category.show')"
                                        class="block transition"
                                        :class="!category ? 'text-[#d10000] font-bold' : 'text-gray-600 hover:text-[#d10000]'"
                                    >Tất cả sản phẩm</Link>
                                    <CategoryTreeItem :nodes="categories" :active-id="category?.id ?? null" />
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="bg-white border border-gray-100 p-5">
                                <h3 class="text-xs font-bold uppercase tracking-widest mb-3">Khoảng giá</h3>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <button
                                        v-for="preset in pricePresets"
                                        :key="preset.label"
                                        type="button"
                                        @click="togglePreset(preset)"
                                        class="px-3 py-1.5 text-[11px] border transition"
                                        :class="isPreset(preset)
                                            ? 'bg-[#d10000] border-[#d10000] text-white font-bold'
                                            : 'bg-white border-gray-200 text-gray-600 hover:border-[#d10000] hover:text-[#d10000]'"
                                    >{{ preset.label }}</button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input v-model="minPrice" type="number" min="0" placeholder="Từ" class="w-full border-gray-200 focus:border-[#d10000] focus:ring-[#d10000] text-xs px-2 py-2" />
                                    <span class="text-gray-400">–</span>
                                    <input v-model="maxPrice" type="number" min="0" placeholder="Đến" class="w-full border-gray-200 focus:border-[#d10000] focus:ring-[#d10000] text-xs px-2 py-2" />
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Products -->
                    <div class="flex-1 min-w-0">
                        <!-- Toolbar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 bg-white border border-gray-100 px-4 py-3 mb-4">
                            <p class="text-sm text-gray-500">
                                <span class="font-bold text-black">{{ products.total }}</span> sản phẩm
                            </p>
                            <label class="flex items-center gap-2 text-sm text-gray-500">
                                <span class="hidden sm:inline text-[11px] font-bold uppercase tracking-widest">Sắp xếp</span>
                                <select v-model="sort" class="border-gray-200 focus:border-[#d10000] focus:ring-[#d10000] text-sm py-1.5 pl-3 pr-8">
                                    <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                </select>
                            </label>
                        </div>

                        <!-- Active filters -->
                        <div v-if="activeFilters.length" class="flex flex-wrap items-center gap-2 mb-4">
                            <button
                                v-for="chip in activeFilters"
                                :key="chip.key"
                                @click="chip.clear()"
                                class="inline-flex items-center gap-2 bg-black text-white text-xs px-3 py-1.5 hover:bg-[#d10000] transition"
                            >
                                {{ chip.label }}
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <button @click="clearAll" class="text-xs text-gray-500 underline hover:text-[#d10000]">Xóa tất cả</button>
                        </div>

                        <div v-if="products.data.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6">
                            <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                        </div>

                        <div v-else class="text-center py-20 bg-white border border-dashed border-gray-200">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <p class="text-gray-500">Không tìm thấy sản phẩm nào phù hợp với bộ lọc của bạn.</p>
                            <button @click="clearAll" class="mt-4 text-[#d10000] underline text-sm font-bold">Xóa bộ lọc</button>
                        </div>

                        <!-- Pagination -->
                        <nav v-if="products.last_page > 1" class="mt-10 flex flex-wrap items-center justify-center gap-2">
                            <template v-for="(link, index) in products.links" :key="`${index}-${link.label}`">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="pageLabel(link, index)"
                                    preserve-scroll
                                    class="min-w-[2.25rem] px-3 py-1.5 text-sm text-center border transition"
                                    :class="link.active
                                        ? 'bg-[#d10000] text-white border-[#d10000] font-bold'
                                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#d10000] hover:text-[#d10000]'"
                                />
                                <span
                                    v-else
                                    v-html="pageLabel(link, index)"
                                    class="min-w-[2.25rem] px-3 py-1.5 text-sm text-center border border-gray-200 text-gray-300 bg-gray-50"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
