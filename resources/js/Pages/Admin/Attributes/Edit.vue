<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { watch, ref } from 'vue';

const props = defineProps({ attribute: Object });

const form = useForm({
    name: props.attribute.name,
    slug: props.attribute.slug,
    type: props.attribute.type,
});

const slugManuallyEdited = ref(true); // edit: giữ slug cũ theo mặc định

function toSlug(str) {
    return str.toLowerCase()
        .normalize('NFD').replace(/[̀-ͯ]/g, '')
        .replace(/đ/g, 'd').replace(/Đ/g, 'd')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim().replace(/\s+/g, '-').replace(/-+/g, '-');
}

watch(() => form.name, (val) => {
    if (!slugManuallyEdited.value) form.slug = toSlug(val);
});

const submit = () => { form.patch(`/admin/attributes/${props.attribute.id}`); };
</script>

<template>
    <AdminLayout>
        <Head title="Sửa Thuộc tính" />

        <div class="max-w-xl space-y-4">
            <div class="flex items-center gap-2 text-sm text-[#50575e]">
                <Link href="/admin/attributes" class="text-[#2271b1] hover:underline">Thuộc tính</Link>
                <span>›</span>
                <span>Sửa: {{ props.attribute.name }}</span>
            </div>

            <div class="bg-white border border-[#c3c4c7] shadow-sm p-6 space-y-4">
                <h1 class="text-lg font-semibold text-[#1d2327]">Sửa Thuộc tính</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1d2327] mb-1">Tên thuộc tính</label>
                        <input v-model="form.name" type="text" class="w-full border-[#8c8f94] rounded text-sm py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                        <p v-if="form.errors.name" class="mt-1 text-red-600 text-xs">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#1d2327] mb-1">Slug</label>
                        <input v-model="form.slug" type="text" @input="slugManuallyEdited = true" class="w-full border-[#8c8f94] rounded text-sm py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                        <p v-if="form.errors.slug" class="mt-1 text-red-600 text-xs">{{ form.errors.slug }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#1d2327] mb-1">Loại</label>
                        <select v-model="form.type" class="w-full border-[#8c8f94] rounded text-sm py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]">
                            <option value="text">Text</option>
                            <option value="color">Color</option>
                            <option value="button">Button</option>
                        </select>
                        <p v-if="form.errors.type" class="mt-1 text-red-600 text-xs">{{ form.errors.type }}</p>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" :disabled="form.processing" class="bg-[#2271b1] text-white px-4 py-1.5 text-sm rounded hover:bg-[#135e96] disabled:opacity-50">
                            {{ form.processing ? 'Đang lưu...' : 'Cập nhật' }}
                        </button>
                        <Link href="/admin/attributes" class="px-4 py-1.5 text-sm border border-[#c3c4c7] rounded hover:bg-[#f6f7f7]">Hủy</Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
