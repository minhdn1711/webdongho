<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';
import CategoryTreeItem from '@/Components/CategoryTreeItem.vue';
import { ref, watch, computed, inject } from 'vue';
import debounce from 'lodash/debounce';

const page = usePage();
const settings = computed(() => page.props.settings || {});

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

const filter = () => {
    router.get(route('category.show', props.category?.slug), {
        search: search.value,
        min_price: minPrice.value,
        max_price: maxPrice.value,
        sort: sort.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([search, minPrice, maxPrice, sort], debounce(() => {
    filter();
}, 500));

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
        <div class="bg-gray-50 py-10">
            <div class="max-w-7xl mx-auto px-4">
                <!-- Breadcrumbs -->
                <nav class="flex mb-8 text-xs font-bold uppercase tracking-widest text-gray-400">
                    <Link :href="'/'" class="hover:text-black">Trang chủ</Link>
                    <span class="mx-2">/</span>
                    <span class="text-black">{{ category ? category.name : 'Tất cả sản phẩm' }}</span>
                </nav>

                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Filters Sidebar -->
                    <aside class="w-full lg:w-64 shrink-0 space-y-10">
                        <!-- Search -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest mb-4 border-b pb-2">Tìm kiếm</h3>
                            <div class="relative">
                                <input 
                                    v-model="search"
                                    type="text" 
                                    placeholder="Tên sản phẩm..." 
                                    class="w-full border-gray-300 focus:border-[#d10000] focus:ring-[#d10000] text-sm px-4 py-2"
                                />
                                <svg class="w-4 h-4 absolute right-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest mb-4 border-b pb-2">Danh mục</h3>
                            <div class="text-sm space-y-2">
                                <Link
                                    :href="route('category.show')"
                                    :class="!category ? 'text-[#d10000] font-bold' : 'text-gray-600 hover:text-[#d10000]'"
                                >Tất cả sản phẩm</Link>
                                <CategoryTreeItem :nodes="categories" :active-id="category?.id ?? null" />
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest mb-4 border-b pb-2">Khoảng giá</h3>
                            <div class="flex items-center gap-2">
                                <input v-model="minPrice" type="number" placeholder="Từ" class="w-full border-gray-300 text-xs px-2 py-2" />
                                <span class="text-gray-400">-</span>
                                <input v-model="maxPrice" type="number" placeholder="Đến" class="w-full border-gray-300 text-xs px-2 py-2" />
                            </div>
                        </div>

                        <!-- Sort -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest mb-4 border-b pb-2">Sắp xếp</h3>
                            <select v-model="sort" class="w-full border-gray-300 text-sm px-2 py-2 focus:ring-[#d10000]">
                                <option value="newest">Mới nhất</option>
                                <option value="price_asc">Giá: Thấp đến Cao</option>
                                <option value="price_desc">Giá: Cao đến Thấp</option>
                                <option value="name_asc">Tên: A-Z</option>
                            </select>
                        </div>
                    </aside>

                    <!-- Product Grid -->
                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-8">
                            <h1 class="text-2xl font-bold uppercase italic text-[#d10000]">{{ category ? category.name : 'Tất cả sản phẩm' }}</h1>
                            <p class="text-sm text-gray-500">Hiển thị {{ products.total }} kết quả</p>
                        </div>

                        <div v-if="products.data.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                        </div>

                        <nav v-if="products.last_page > 1" class="mt-10 flex flex-wrap items-center justify-center gap-2">
                            <template v-for="(link, index) in products.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="pageLabel(link, index)"
                                    preserve-scroll
                                    class="min-w-[2.25rem] px-3 py-1.5 text-sm text-center border transition"
                                    :class="link.active
                                        ? 'bg-[#d10000] text-white border-[#d10000] font-bold'
                                        : 'bg-white text-gray-600 border-gray-300 hover:border-[#d10000] hover:text-[#d10000]'"
                                />
                                <span
                                    v-else
                                    v-html="pageLabel(link, index)"
                                    class="min-w-[2.25rem] px-3 py-1.5 text-sm text-center border border-gray-200 text-gray-300 bg-gray-50"
                                />
                            </template>
                        </nav>

                        <div v-else class="text-center py-20 bg-white shadow-sm border border-dashed">
                            <p class="text-gray-500 italic">Không tìm thấy sản phẩm nào phù hợp với bộ lọc của bạn.</p>
                            <button @click="search = ''; minPrice = ''; maxPrice = '';" class="mt-4 text-[#d10000] underline text-sm font-bold">Xóa bộ lọc</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
