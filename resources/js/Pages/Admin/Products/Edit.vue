<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MediaLibrary from '@/Components/MediaLibrary.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import RichEditor from '@/Components/RichEditor.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    product: Object,
    categories: Array,
    attributes: Array,
    products: { type: Array, default: () => [] },
});

const imagePreview = ref(null);
const showMediaLibrary = ref(false);
const showGalleryLibrary = ref(false);
const showAttributeImageLibrary = ref(false);
const activeAttributeForImage = ref(null);

// Gallery management
// Items: { id: number|null, url: string, file: File|null, preview: string }
const galleryItems = ref(
    (props.product.product_images || []).map(img => ({
        id: img.id,
        url: img.image_url,
        file: null,
        preview: img.image_url,
    }))
);
const deletedIds = ref([]);

const driveUrl = ref('');
const driveImages = ref([]);
const selectedDriveImages = ref([]);
const loadingDrive = ref(false);

const form = useForm({
    _method: 'put',
    category_ids: props.product.categories.map(c => c.id),
    name: props.product.name,
    short_description: props.product.short_description,
    description: props.product.description,
    price: props.product.price,
    sale_price: props.product.sale_price,
    image: props.product.image,
    image_file: null,
    stock: props.product.stock,
    is_featured: props.product.is_featured === 1 || props.product.is_featured === true,
    sku: props.product.sku,
    barcode: props.product.barcode,
    product_attributes: Object.fromEntries(
        (props.attributes || []).map(attr => [
            attr.id,
            (props.product.product_attribute_values || [])
                .filter(pav => pav.attribute_id === attr.id)
                .map(pav => pav.attribute_value_id)
        ])
    ),
    attribute_images: Object.fromEntries(
        (props.product.product_attribute_values || [])
            .filter(pav => pav.image_url)
            .map(pav => [pav.attribute_value_id, pav.image_url])
    ),
    related_product_ids: (props.product.related_products || []).map(p => p.id),
});

const pancakeConfigured = usePage().props.pancake_configured;
const showPancakeModal = ref(false);

onMounted(() => {
    if (!pancakeConfigured) {
        showPancakeModal.value = true;
    }
});

const closePancakeModal = () => { showPancakeModal.value = false; };

// Related products
const relatedSearch = ref('');
const filteredProducts = computed(() => {
    const q = relatedSearch.value.toLowerCase().trim();
    if (!q) return props.products || [];
    return (props.products || []).filter(p => p.name.toLowerCase().includes(q));
});
const toggleRelated = (id) => {
    const idx = form.related_product_ids.indexOf(id);
    if (idx >= 0) {
        form.related_product_ids.splice(idx, 1);
    } else {
        form.related_product_ids.push(id);
    }
};
const selectedRelatedProducts = computed(() =>
    (props.products || []).filter(p => form.related_product_ids.includes(p.id))
);

// Main image handlers
const handleImageSelect = (image) => {
    form.image = image.url;
    imagePreview.value = image.url;
    form.image_file = null;
};

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_file = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    imagePreview.value = null;
    form.image_file = null;
    form.image = '';
};

const openAttributeImageLibrary = (valueId) => {
    activeAttributeForImage.value = valueId;
    showAttributeImageLibrary.value = true;
};

const handleAttributeImageSelect = (image) => {
    if (activeAttributeForImage.value) {
        form.attribute_images[activeAttributeForImage.value] = image.url;
    }
};

// Gallery handlers
const handleGalleryLibrarySelect = (images) => {
    for (const img of images) {
        if (!galleryItems.value.some(i => i.url === img.url)) {
            galleryItems.value.push({ id: null, url: img.url, file: null, preview: img.url });
        }
    }
};

const handleGalleryUpload = (e) => {
    const files = Array.from(e.target.files);
    for (const file of files) {
        galleryItems.value.push({ id: null, url: '', file, preview: URL.createObjectURL(file) });
    }
    e.target.value = '';
};

const removeGalleryItem = (index) => {
    const item = galleryItems.value[index];
    if (item.id) {
        deletedIds.value.push(item.id);
    }
    galleryItems.value.splice(index, 1);
};

// Google Drive handlers
const fetchDriveImages = async () => {
    if (!driveUrl.value.trim()) return;
    loadingDrive.value = true;
    driveImages.value = [];
    selectedDriveImages.value = [];
    try {
        const res = await axios.post(route('admin.products.fetch-drive'), {
            folder_url: driveUrl.value
        });
        driveImages.value = res.data.images || [];
        if (driveImages.value.length === 0) {
            alert('Không tìm thấy ảnh nào trong folder này.');
        }
    } catch (e) {
        alert(e.response?.data?.error || 'Lỗi khi lấy ảnh từ Google Drive.');
    } finally {
        loadingDrive.value = false;
    }
};

const toggleDriveImage = (img) => {
    const idx = selectedDriveImages.value.findIndex(i => i.id === img.id);
    if (idx >= 0) {
        selectedDriveImages.value.splice(idx, 1);
    } else {
        selectedDriveImages.value.push(img);
    }
};

const isDriveSelected = (img) => selectedDriveImages.value.some(i => i.id === img.id);

const selectAllDriveImages = () => {
    if (selectedDriveImages.value.length === driveImages.value.length) {
        selectedDriveImages.value = [];
    } else {
        selectedDriveImages.value = [...driveImages.value];
    }
};

const addSelectedDriveImages = () => {
    for (const img of selectedDriveImages.value) {
        if (!galleryItems.value.some(i => i.url === img.url)) {
            galleryItems.value.push({ id: null, url: img.url, file: null, preview: img.url });
        }
    }
    driveImages.value = [];
    selectedDriveImages.value = [];
    driveUrl.value = '';
};

const submit = () => {
    form.transform(data => ({
        ...data,
        gallery_urls: galleryItems.value.filter(i => !i.file && i.id === null).map(i => i.url),
        gallery_files: galleryItems.value.filter(i => !!i.file).map(i => i.file),
        delete_image_ids: deletedIds.value,
    })).post(route('admin.products.update', props.product.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Sửa sản phẩm" />

    <AdminLayout>
        <template #header>
            <h1 class="text-[23px] font-normal text-[#1d2327] leading-tight">Chỉnh sửa sản phẩm</h1>
        </template>

        <form @submit.prevent="submit">
            <div class="flex flex-col lg:flex-row gap-5">
                <!-- ===== LEFT COLUMN ===== -->
                <div class="flex-1 min-w-0 space-y-5">
                    <!-- Title -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Nhập tên sản phẩm tại đây"
                            class="w-full border-0 border-b border-[#dcdcde] text-[#1d2327] text-lg py-3 px-3 focus:ring-0 focus:border-[#2271b1] focus:shadow-[0_0_0_1px_#2271b1] placeholder-[#a7aaad]"
                            required
                        />
                        <div v-if="form.errors.name" class="text-[#d63638] text-xs px-3 py-1 bg-[#fcf0f1]">{{ form.errors.name }}</div>
                    </div>

                    <!-- Short Description -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Mô tả ngắn (Hiển thị đầu trang chi tiết)</span>
                        </div>
                        <div class="p-3">
                            <textarea
                                v-model="form.short_description"
                                rows="3"
                                placeholder="Nhập mô tả ngắn gọn về sản phẩm..."
                                class="w-full border-[#8c8f94] rounded text-[13px] py-2 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                            ></textarea>
                        </div>
                        <div v-if="form.errors.short_description" class="text-[#d63638] text-xs px-3 py-1 bg-[#fcf0f1]">{{ form.errors.short_description }}</div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Mô tả chi tiết</span>
                        </div>
                        <div class="p-0">
                            <RichEditor v-model="form.description" placeholder="Nhập mô tả chi tiết sản phẩm..." />
                        </div>
                        <div v-if="form.errors.description" class="text-[#d63638] text-xs px-3 py-1 bg-[#fcf0f1]">{{ form.errors.description }}</div>
                    </div>

                    <!-- Product Data -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Dữ liệu sản phẩm</span>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Giá gốc (₫)</label>
                                <input v-model="form.price" type="number" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" required />
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Giá khuyến mãi (₫)</label>
                                <input v-model="form.sale_price" type="number" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Tồn kho</label>
                                <input v-model="form.stock" type="number" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" required />
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">SKU (Mã sản phẩm)</label>
                                <input v-model="form.sku" type="text" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="Ví dụ: SP001" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Barcode (Mã vạch)</label>
                                <input v-model="form.barcode" type="text" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="Ví dụ: 893..." />
                            </div>
                        </div>
                    </div>

                    <!-- Product Attributes -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm" v-if="attributes && attributes.length > 0">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Thuộc tính sản phẩm</span>
                        </div>
                        <div class="p-4 space-y-4">
                            <div v-for="attribute in attributes" :key="attribute.id">
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-2">{{ attribute.name }}</label>
                                <div v-if="attribute.type === 'text'" class="space-y-2">
                                    <div v-for="value in attribute.attribute_values" :key="value.id" class="flex items-center">
                                        <input
                                            type="checkbox"
                                            :id="'attr-' + value.id"
                                            :value="value.id"
                                            v-model="form.product_attributes[attribute.id]"
                                            class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-4 w-4"
                                        />
                                        <label :for="'attr-' + value.id" class="ml-2 text-[13px] text-[#50575e] cursor-pointer hover:text-[#2271b1]">{{ value.value }}</label>
                                    </div>
                                </div>
                                <div v-else-if="attribute.type === 'color'" class="space-y-3">
                                    <div v-for="value in attribute.attribute_values" :key="value.id" class="flex items-center flex-wrap gap-2">
                                        <div class="flex items-center w-40">
                                            <input
                                                type="checkbox"
                                                :id="'attr-' + value.id"
                                                :value="value.id"
                                                v-model="form.product_attributes[attribute.id]"
                                                class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-4 w-4"
                                            />
                                            <label :for="'attr-' + value.id" class="ml-2 cursor-pointer flex items-center gap-2">
                                                <div :style="{ backgroundColor: value.meta_value }" class="w-6 h-6 border border-gray-300 rounded"></div>
                                                <span class="text-[13px] text-[#50575e]">{{ value.value }}</span>
                                            </label>
                                        </div>
                                        <div v-if="form.product_attributes[attribute.id].includes(value.id)" class="flex items-center gap-2 ml-4">
                                            <div class="flex flex-col gap-1">
                                                <input v-model="form.attribute_images[value.id]" type="text" placeholder="URL ảnh (Tùy chọn)" class="border-[#8c8f94] rounded text-[12px] py-1 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1] w-48" />
                                                <button type="button" @click.prevent="openAttributeImageLibrary(value.id)" class="text-[11px] text-[#2271b1] hover:underline text-left">Chọn từ thư viện</button>
                                            </div>
                                            <img v-if="form.attribute_images[value.id]" :src="form.attribute_images[value.id]" class="w-10 h-10 object-cover rounded border border-gray-200" />
                                            <button v-if="form.attribute_images[value.id]" @click.prevent="form.attribute_images[value.id] = ''" type="button" class="text-red-500 hover:text-red-700" title="Xóa ảnh">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else-if="attribute.type === 'button'" class="flex flex-wrap gap-2">
                                    <label v-for="value in attribute.attribute_values" :key="value.id" class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            :value="value.id"
                                            v-model="form.product_attributes[attribute.id]"
                                            class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-4 w-4"
                                        />
                                        <span class="px-3 py-1 border border-[#c3c4c7] rounded text-[13px] text-[#50575e] hover:border-[#2271b1] cursor-pointer">{{ value.value }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ===== RIGHT SIDEBAR ===== -->
                <div class="w-full lg:w-[280px] shrink-0 space-y-5">

                    <!-- Publish Box -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Cập nhật</span>
                        </div>
                        <div class="p-3 space-y-3">
                            <div class="flex items-center gap-2 text-[13px] text-[#50575e]">
                                <svg class="w-4 h-4 text-[#8c8f94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Trạng thái: <strong class="text-[#1d2327]">Đã xuất bản</strong>
                            </div>
                            <div class="flex items-center gap-2 text-[13px] text-[#50575e]">
                                <svg class="w-4 h-4 text-[#8c8f94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Ngày tạo: <strong class="text-[#1d2327]">{{ new Date(product.created_at).toLocaleDateString('vi-VN') }}</strong>
                            </div>
                            <div class="flex items-center">
                                <input v-model="form.is_featured" type="checkbox" id="is_featured" class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-4 w-4" />
                                <label for="is_featured" class="ml-2 text-[13px] text-[#50575e] cursor-pointer">Sản phẩm nổi bật</label>
                            </div>
                        </div>
                        <div class="border-t border-[#c3c4c7] bg-[#f6f7f7] px-3 py-2 flex justify-between items-center">
                            <Link :href="route('admin.products.index')" class="text-[13px] text-[#b32d2e] hover:text-[#a02929] hover:underline">Quay lại</Link>
                            <button type="submit" :disabled="form.processing"
                                class="bg-[#2271b1] hover:bg-[#135e96] text-white text-[13px] font-medium px-4 py-1.5 rounded-sm border border-[#2271b1] transition disabled:opacity-50">
                                {{ form.processing ? 'Đang lưu...' : 'Cập nhật' }}
                            </button>
                        </div>
                    </div>

                    <!-- Category Box -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Danh mục</span>
                        </div>
                        <div class="p-3">
                            <div class="max-h-[200px] overflow-y-auto border border-[#dcdcde] p-3 space-y-2">
                                <div v-for="cat in categories" :key="cat.id" class="flex items-center">
                                    <input type="checkbox" :id="'cat-'+cat.id" :value="cat.id" v-model="form.category_ids"
                                        class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-4 w-4" />
                                    <label :for="'cat-'+cat.id" class="ml-2 text-[13px] text-[#50575e] cursor-pointer hover:text-[#2271b1]">{{ cat.name }}</label>
                                </div>
                            </div>
                            <div v-if="form.errors.category_ids" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.category_ids }}</div>
                        </div>
                    </div>

                    <!-- Sản phẩm liên quan -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2 flex items-center justify-between">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Sản phẩm liên quan</span>
                            <span v-if="form.related_product_ids.length" class="text-[11px] bg-[#2271b1] text-white px-1.5 py-0.5 rounded-full">{{ form.related_product_ids.length }}</span>
                        </div>
                        <div class="p-3">
                            <!-- Selected chips -->
                            <div v-if="selectedRelatedProducts.length > 0" class="flex flex-wrap gap-1 mb-2">
                                <span v-for="p in selectedRelatedProducts" :key="p.id"
                                    class="inline-flex items-center gap-1 bg-[#e8f0fb] text-[#2271b1] text-[11px] px-2 py-0.5 rounded-full">
                                    {{ p.name.length > 20 ? p.name.slice(0, 20) + '…' : p.name }}
                                    <button type="button" @click="toggleRelated(p.id)" class="text-[#2271b1] hover:text-[#b32d2e] leading-none">&times;</button>
                                </span>
                            </div>
                            <!-- Search -->
                            <input v-model="relatedSearch" type="text" placeholder="Tìm sản phẩm..."
                                class="w-full border-[#8c8f94] rounded text-[12px] py-1 mb-2 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                            <!-- List -->
                            <div class="max-h-[180px] overflow-y-auto border border-[#dcdcde] rounded">
                                <div v-if="filteredProducts.length === 0" class="text-[11px] text-[#8c8f94] text-center py-3">Không tìm thấy</div>
                                <div v-for="p in filteredProducts" :key="p.id"
                                    class="flex items-center px-2 py-1.5 hover:bg-[#f6f7f7] cursor-pointer border-b border-[#f0f0f1] last:border-0"
                                    @click="toggleRelated(p.id)">
                                    <input type="checkbox" :checked="form.related_product_ids.includes(p.id)"
                                        class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-3.5 w-3.5 pointer-events-none" />
                                    <span class="ml-2 text-[12px] text-[#50575e]">{{ p.name }}</span>
                                </div>
                            </div>
                            <p class="text-[10px] text-[#8c8f94] mt-1.5">Nếu không chọn, hệ thống tự hiển thị sản phẩm cùng danh mục.</p>
                        </div>
                    </div>

                    <!-- Ảnh đại diện -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Ảnh đại diện</span>
                        </div>
                        <div class="p-3">
                            <div v-if="imagePreview || form.image" class="mb-3 relative">
                                <img :src="imagePreview || form.image" class="w-full h-auto rounded border border-[#c3c4c7] object-cover max-h-[200px]" />
                                <button @click.prevent="removeImage" type="button"
                                    class="absolute top-1 right-1 bg-white/90 text-[#b32d2e] rounded-full p-1 shadow-sm hover:bg-red-50 transition border border-[#c3c4c7]">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="flex flex-col gap-2 mt-2">
                                <button type="button" @click="showMediaLibrary = true" class="text-[13px] text-[#2271b1] hover:text-[#135e96] font-medium text-left">
                                    {{ imagePreview || form.image ? 'Thay đổi từ thư viện' : 'Chọn từ thư viện' }}
                                </button>
                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                                    <div class="relative flex justify-center text-[10px] uppercase"><span class="bg-white px-2 text-gray-400">Hoặc</span></div>
                                </div>
                                <input type="file" id="image_upload" @change="handleImageChange" class="hidden" accept="image/*" />
                                <label for="image_upload" class="inline-block text-[13px] text-[#2271b1] hover:text-[#135e96] hover:underline cursor-pointer">
                                    Tải ảnh mới từ máy tính
                                </label>
                            </div>
                            <div v-if="form.errors.image_file" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.image_file }}</div>
                            <div class="mt-3 pt-3 border-t border-[#dcdcde]">
                                <label class="text-[11px] text-[#8c8f94] uppercase tracking-wider block mb-1">Hoặc nhập URL</label>
                                <input v-model="form.image" type="text" class="w-full border-[#8c8f94] rounded text-[12px] py-1 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="https://..." />
                            </div>
                        </div>
                    </div>

                    <!-- Thư viện ảnh sản phẩm -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2 flex items-center justify-between">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Thư viện ảnh sản phẩm</span>
                            <span class="text-[11px] text-[#8c8f94]">{{ galleryItems.length }} ảnh</span>
                        </div>
                        <div class="p-3">
                            <!-- Preview grid -->
                            <div v-if="galleryItems.length > 0" class="grid grid-cols-3 gap-1.5 mb-3">
                                <div v-for="(item, index) in galleryItems" :key="index" class="relative aspect-square group">
                                    <img :src="item.preview || item.url" class="w-full h-full object-cover border border-gray-200 rounded" />
                                    <button @click.prevent="removeGalleryItem(index)" type="button"
                                        class="absolute top-0.5 right-0.5 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-sm leading-none opacity-0 group-hover:opacity-100 transition shadow">×</button>
                                    <div v-if="item.id === null" class="absolute bottom-0.5 left-0.5 bg-green-500 text-white text-[8px] px-1 rounded">MỚI</div>
                                </div>
                            </div>
                            <div v-else class="text-[11px] text-[#8c8f94] text-center py-4 border border-dashed border-[#c3c4c7] rounded mb-3">
                                Chưa có ảnh
                            </div>

                            <!-- Buttons -->
                            <div class="flex flex-col gap-1.5 mb-3">
                                <button type="button" @click="showGalleryLibrary = true"
                                    class="text-[12px] border border-[#2271b1] text-[#2271b1] hover:bg-[#2271b1] hover:text-white px-3 py-1.5 rounded transition w-full text-left">
                                    + Chọn từ thư viện
                                </button>
                                <label for="gallery_upload"
                                    class="text-[12px] border border-[#2271b1] text-[#2271b1] hover:bg-[#2271b1] hover:text-white px-3 py-1.5 rounded transition cursor-pointer w-full">
                                    + Tải ảnh từ máy tính
                                </label>
                                <input type="file" id="gallery_upload" multiple accept="image/*" class="hidden" @change="handleGalleryUpload" />
                            </div>

                            <!-- Google Drive -->
                            <div class="pt-3 border-t border-[#dcdcde]">
                                <label class="text-[11px] font-semibold text-[#50575e] uppercase tracking-wider block mb-1.5">
                                    Google Drive folder
                                </label>
                                <div class="flex gap-1.5">
                                    <input v-model="driveUrl" type="text"
                                        class="flex-1 border-[#8c8f94] rounded text-[11px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                                        placeholder="https://drive.google.com/drive/folders/..."
                                        @keyup.enter="fetchDriveImages" />
                                    <button type="button" @click="fetchDriveImages" :disabled="loadingDrive || !driveUrl.trim()"
                                        class="text-[11px] bg-[#2271b1] text-white px-2 py-1.5 rounded hover:bg-[#135e96] disabled:opacity-50 transition whitespace-nowrap">
                                        {{ loadingDrive ? '...' : 'Lấy ảnh' }}
                                    </button>
                                </div>

                                <!-- Drive result -->
                                <div v-if="driveImages.length > 0" class="mt-3">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[11px] text-[#1d2327] font-semibold">{{ driveImages.length }} ảnh tìm thấy</span>
                                        <button type="button" @click="selectAllDriveImages" class="text-[10px] text-[#2271b1] hover:underline">
                                            {{ selectedDriveImages.length === driveImages.length ? 'Bỏ tất cả' : 'Chọn tất cả' }}
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1.5 mb-2 max-h-[180px] overflow-y-auto">
                                        <div v-for="img in driveImages" :key="img.id" @click="toggleDriveImage(img)"
                                            class="relative aspect-square cursor-pointer rounded overflow-hidden border-2 transition"
                                            :class="isDriveSelected(img) ? 'border-[#2271b1]' : 'border-transparent hover:border-gray-300'">
                                            <img :src="img.url" class="w-full h-full object-cover" :alt="img.name" />
                                            <div v-if="isDriveSelected(img)" class="absolute top-0.5 right-0.5 bg-[#2271b1] text-white rounded-full w-4 h-4 flex items-center justify-center text-[9px]">✓</div>
                                        </div>
                                    </div>
                                    <button type="button" @click="addSelectedDriveImages" :disabled="selectedDriveImages.length === 0"
                                        class="text-[11px] bg-[#2271b1] text-white px-3 py-1.5 rounded hover:bg-[#135e96] disabled:opacity-50 transition w-full">
                                        Thêm {{ selectedDriveImages.length > 0 ? selectedDriveImages.length + ' ảnh' : '' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <!-- Media Library - single select for main image -->
        <MediaLibrary
            :show="showMediaLibrary"
            @close="showMediaLibrary = false"
            @select="handleImageSelect"
        />

        <!-- Media Library - multi select for gallery -->
        <MediaLibrary
            :show="showGalleryLibrary"
            title="Chọn ảnh cho thư viện sản phẩm"
            :multiSelect="true"
            @close="showGalleryLibrary = false"
            @select="handleGalleryLibrarySelect"
        />

        <!-- Media Library - for attribute image -->
        <MediaLibrary
            :show="showAttributeImageLibrary"
            @close="showAttributeImageLibrary = false"
            @select="handleAttributeImageSelect"
        />

        <!-- Pancake Config Warning Modal -->
        <Modal :show="showPancakeModal" @close="closePancakeModal">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4 text-[#d63638]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h2 class="text-lg font-medium text-gray-900">Cấu hình Pancake POS chưa hoàn tất</h2>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Bạn cần cài đặt cấu hình Pancake POS (API Token và Shop ID) để có thể đồng bộ sản phẩm về hệ thống POS. Nếu không cấu hình, sản phẩm sẽ chỉ được lưu trên website.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="closePancakeModal">Để sau</SecondaryButton>
                    <Link
                        :href="route('admin.pancake.settings')"
                        class="inline-flex items-center px-4 py-2 bg-[#2271b1] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#135e96] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Cài đặt ngay
                    </Link>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
