<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import RichEditor from '@/Components/RichEditor.vue';

defineProps({
    categories: Array,
});

const imagePreview = ref(null);

const form = useForm({
    category_id: '',
    name: '',
    short_description: '',
    description: '',
    price: 0,
    sale_price: null,
    image: '',
    image_file: null,
    stock: 0,
    is_featured: false,
    sku: '',
    barcode: '',
});

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
};

const submit = () => {
    form.post(route('admin.products.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Thêm sản phẩm mới" />

    <AdminLayout>
        <template #header>
            <h1 class="text-[23px] font-normal text-[#1d2327] leading-tight">
                Thêm sản phẩm mới
            </h1>
        </template>

        <form @submit.prevent="submit">
            <div class="flex flex-col lg:flex-row gap-5">
                <!-- ===== LEFT COLUMN (Main Content) ===== -->
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

                    <!-- Description (Content Editor) -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Mô tả chi tiết</span>
                        </div>
                        <div class="p-0">
                            <RichEditor
                                v-model="form.description"
                                placeholder="Nhập mô tả chi tiết sản phẩm..."
                            />
                        </div>
                        <div v-if="form.errors.description" class="text-[#d63638] text-xs px-3 py-1 bg-[#fcf0f1]">{{ form.errors.description }}</div>
                    </div>

                    <!-- Product Data (Pricing / Stock) -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2 flex items-center justify-between">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Dữ liệu sản phẩm</span>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Giá gốc (₫)</label>
                                <input v-model="form.price" type="number" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" required />
                                <div v-if="form.errors.price" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.price }}</div>
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Giá khuyến mãi (₫)</label>
                                <input v-model="form.sale_price" type="number" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                                <div v-if="form.errors.sale_price" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.sale_price }}</div>
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-[#1d2327] uppercase tracking-wider mb-1.5">Tồn kho</label>
                                <input v-model="form.stock" type="number" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" required />
                                <div v-if="form.errors.stock" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.stock }}</div>
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
                </div>

                <!-- ===== RIGHT SIDEBAR (Meta Boxes) ===== -->
                <div class="w-full lg:w-[280px] shrink-0 space-y-5">

                    <!-- Publish Box -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Đăng</span>
                        </div>
                        <div class="p-3 space-y-3">
                            <div class="flex items-center gap-2 text-[13px] text-[#50575e]">
                                <svg class="w-4 h-4 text-[#8c8f94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Trạng thái: <strong class="text-[#1d2327]">Bản nháp</strong>
                            </div>
                            <div class="flex items-center">
                                <input v-model="form.is_featured" type="checkbox" id="is_featured" class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-4 w-4" />
                                <label for="is_featured" class="ml-2 text-[13px] text-[#50575e] cursor-pointer">Sản phẩm nổi bật</label>
                            </div>
                        </div>
                        <div class="border-t border-[#c3c4c7] bg-[#f6f7f7] px-3 py-2 flex justify-between items-center">
                            <Link :href="route('admin.products.index')" class="text-[13px] text-[#b32d2e] hover:text-[#a02929] hover:underline">
                                Hủy
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-[#2271b1] hover:bg-[#135e96] text-white text-[13px] font-medium px-4 py-1.5 rounded-sm border border-[#2271b1] transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Đang lưu...' : 'Đăng sản phẩm' }}
                            </button>
                        </div>
                    </div>

                    <!-- Category Box -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Danh mục</span>
                        </div>
                        <div class="p-3">
                            <select v-model="form.category_id" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" required>
                                <option value="" disabled>— Chọn danh mục —</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <div v-if="form.errors.category_id" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.category_id }}</div>
                        </div>
                    </div>

                    <!-- Featured Image Box -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Ảnh sản phẩm</span>
                        </div>
                        <div class="p-3">
                            <!-- Preview Area -->
                            <div v-if="imagePreview || form.image" class="mb-3 relative">
                                <img
                                    :src="imagePreview || form.image"
                                    class="w-full h-auto rounded border border-[#c3c4c7] object-cover max-h-[200px]"
                                />
                                <button
                                    @click.prevent="removeImage(); form.image = ''"
                                    type="button"
                                    class="absolute top-1 right-1 bg-white/90 text-[#b32d2e] rounded-full p-1 shadow-sm hover:bg-red-50 transition border border-[#c3c4c7]"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <!-- Upload Button -->
                            <input type="file" id="image_upload" @change="handleImageChange" class="hidden" accept="image/*" />
                            <label
                                for="image_upload"
                                class="inline-block text-[13px] text-[#2271b1] hover:text-[#135e96] hover:underline cursor-pointer"
                            >
                                {{ imagePreview || form.image ? 'Thay đổi ảnh sản phẩm' : 'Đặt ảnh sản phẩm' }}
                            </label>
                            <div v-if="form.errors.image_file" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.image_file }}</div>

                            <!-- URL Fallback -->
                            <div class="mt-3 pt-3 border-t border-[#dcdcde]">
                                <label class="text-[11px] text-[#8c8f94] uppercase tracking-wider block mb-1">Hoặc nhập URL</label>
                                <input v-model="form.image" type="text" class="w-full border-[#8c8f94] rounded text-[12px] py-1 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="https://..." />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
