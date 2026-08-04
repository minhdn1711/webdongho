<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ feature: Object });

const isEdit = computed(() => !!props.feature);

const form = useForm({
    _method: isEdit.value ? 'put' : 'post',
    title: props.feature?.title ?? '',
    description: props.feature?.description ?? '',
    icon_svg: props.feature?.icon_svg ?? '',
    order: props.feature?.order ?? 0,
    is_active: props.feature?.is_active ?? true,
});

const submit = () => {
    const url = isEdit.value
        ? route('admin.site-features.update', props.feature.id)
        : route('admin.site-features.store');
    form.post(url);
};

const ICON_PRESETS = [
    { label: 'Dấu tích', svg: '<path d="M5 13l4 4L19 7" />' },
    { label: 'Đồng hồ', svg: '<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />' },
    { label: 'Hộp', svg: '<path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />' },
    { label: 'Xe giao hàng', svg: '<path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61H17a2 2 0 001.98-1.72L21 6H6" />' },
    { label: 'Khiên bảo hành', svg: '<path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />' },
    { label: 'Ngôi sao', svg: '<path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />' },
    { label: 'Tim', svg: '<path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />' },
    { label: 'Quà', svg: '<path d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />' },
];
</script>

<template>
    <Head :title="isEdit ? 'Sửa tính năng' : 'Thêm tính năng'" />
    <AdminLayout>
        <div class="max-w-2xl mx-auto py-8 px-4">
            <div class="flex items-center gap-3 mb-6">
                <Link :href="route('admin.site-features.index')" class="text-gray-400 hover:text-gray-700">← Quay lại</Link>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Sửa tính năng' : 'Thêm tính năng mới' }}</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded shadow p-6 space-y-6">

                <!-- Title -->
                <div>
                    <label class="block text-sm font-semibold mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                    <input v-model="form.title" type="text" class="w-full border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="VD: 100% Chính hãng" />
                    <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold mb-1">Mô tả</label>
                    <textarea v-model="form.description" rows="3" class="w-full border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Mô tả ngắn hiển thị dưới tiêu đề..."></textarea>
                    <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
                </div>

                <!-- Icon -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Icon (SVG path)</label>

                    <!-- Presets -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        <button v-for="preset in ICON_PRESETS" :key="preset.label" type="button"
                            @click="form.icon_svg = preset.svg"
                            :class="['flex flex-col items-center gap-1 px-3 py-2 border rounded text-xs transition',
                                form.icon_svg === preset.svg ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-400']">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="preset.svg"></svg>
                            {{ preset.label }}
                        </button>
                    </div>

                    <!-- Custom SVG input -->
                    <textarea v-model="form.icon_svg" rows="3" class="w-full border-gray-300 rounded px-3 py-2 font-mono text-xs focus:ring-blue-500 focus:border-blue-500" placeholder='<path d="M5 13l4 4L19 7" />'></textarea>
                    <p class="text-xs text-gray-400 mt-1">Chọn icon có sẵn hoặc dán nội dung bên trong thẻ &lt;svg&gt;...</p>

                    <!-- Preview -->
                    <div v-if="form.icon_svg" class="mt-3 flex items-center gap-3">
                        <span class="text-xs text-gray-500">Xem trước:</span>
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="form.icon_svg"></svg>
                        </div>
                    </div>
                </div>

                <!-- Order & Active -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Thứ tự hiển thị</label>
                        <input v-model.number="form.order" type="number" min="0" class="w-full border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Trạng thái</label>
                        <label class="flex items-center gap-2 cursor-pointer mt-2">
                            <input type="checkbox" v-model="form.is_active" class="rounded" />
                            <span class="text-sm">Hiển thị trên trang chủ</span>
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-3 pt-2">
                    <Link :href="route('admin.site-features.index')" class="px-4 py-2 border rounded hover:bg-gray-50">Hủy</Link>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 font-medium">
                        {{ isEdit ? 'Lưu thay đổi' : 'Thêm mới' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
