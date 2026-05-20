<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    attribute: Object,
});

import { ref, watch } from 'vue';

const form = useForm({
    value: '',
    slug: '',
    meta_value: '#000000',
});

const slugManuallyEdited = ref(false);

function toSlug(str) {
    return str
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
}

watch(() => form.value, (val) => {
    if (!slugManuallyEdited.value) form.slug = toSlug(val);
});

const submit = () => {
    form.post(`/admin/attributes/${props.attribute.id}/values`);
};
</script>

<template>
    <AdminLayout>
        <Head title="Thêm Giá trị" />

        <div class="max-w-xl space-y-4">
            <div class="flex items-center gap-2 text-sm text-[#50575e]">
                <Link href="/admin/attributes" class="text-[#2271b1] hover:underline">Thuộc tính</Link>
                <span>›</span>
                <Link :href="`/admin/attributes/${props.attribute.id}/values`" class="text-[#2271b1] hover:underline">{{ props.attribute.name }}</Link>
                <span>›</span>
                <span>Thêm giá trị</span>
            </div>

            <div class="bg-white border border-[#c3c4c7] shadow-sm p-6 space-y-4">
                <h1 class="text-lg font-semibold text-[#1d2327]">Thêm Giá trị cho "{{ props.attribute.name }}"</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1d2327] mb-1">Giá trị</label>
                        <input v-model="form.value" type="text" class="w-full border-[#8c8f94] rounded text-sm py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="Ví dụ: Đỏ" />
                        <p v-if="form.errors.value" class="mt-1 text-red-600 text-xs">{{ form.errors.value }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#1d2327] mb-1">Slug</label>
                        <input v-model="form.slug" type="text" @input="slugManuallyEdited = true" class="w-full border-[#8c8f94] rounded text-sm py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="Tự động sinh từ giá trị" />
                        <p v-if="form.errors.slug" class="mt-1 text-red-600 text-xs">{{ form.errors.slug }}</p>
                    </div>

                    <div v-if="props.attribute.type === 'color'">
                        <label class="block text-sm font-medium text-[#1d2327] mb-1">Mã màu (Hex)</label>
                        <div class="flex gap-2">
                            <input v-model="form.meta_value" type="color" class="w-12 h-9 border border-[#8c8f94] rounded cursor-pointer p-0.5" />
                            <input v-model="form.meta_value" type="text" class="flex-1 border-[#8c8f94] rounded text-sm py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" placeholder="#FF0000" />
                        </div>
                        <p v-if="form.errors.meta_value" class="mt-1 text-red-600 text-xs">{{ form.errors.meta_value }}</p>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" :disabled="form.processing" class="bg-[#2271b1] text-white px-4 py-1.5 text-sm rounded hover:bg-[#135e96] disabled:opacity-50">
                            {{ form.processing ? 'Đang lưu...' : 'Thêm Giá trị' }}
                        </button>
                        <Link :href="`/admin/attributes/${props.attribute.id}/values`" class="px-4 py-1.5 text-sm border border-[#c3c4c7] rounded hover:bg-[#f6f7f7]">Hủy</Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
