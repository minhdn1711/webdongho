<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Storage } from '@/Utils/Storage'; // Assuming there might be a helper, but I'll use direct URL

const props = defineProps({
    banner: Object,
});

const isEdit = !!props.banner;
const imagePreview = ref(props.banner ? props.banner.image_url : null);

const form = useForm({
    _method: isEdit ? 'put' : 'post',
    title: props.banner?.title ?? '',
    link: props.banner?.link ?? '',
    order: props.banner?.order ?? 0,
    is_active: props.banner?.is_active ?? true,
    image: null,
});

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    const url = isEdit ? route('admin.banners.update', props.banner.id) : route('admin.banners.store');
    
    // Inertia doesn't support PUT with multipart/form-data directly, 
    // but Laravel handles _method: 'PUT' in a POST request.
    form.post(url, {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="isEdit ? 'Sửa Banner' : 'Thêm Banner mới'" />

    <AdminLayout>
        <template #header>
            <h1 class="text-[23px] font-normal text-[#1d2327]">
                {{ isEdit ? 'Sửa Banner' : 'Thêm Banner mới' }}
            </h1>
        </template>

        <form @submit.prevent="submit">
            <div class="flex flex-col lg:flex-row gap-5">
                <!-- Left Column -->
                <div class="flex-1 space-y-5">
                    <div class="bg-white border border-[#c3c4c7] shadow-sm p-4 space-y-4">
                        <div>
                            <label class="block text-[13px] font-semibold text-[#1d2327] mb-1">Tiêu đề (Không bắt buộc)</label>
                            <input v-model="form.title" type="text" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                            <div v-if="form.errors.title" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.title }}</div>
                        </div>

                        <div>
                            <label class="block text-[13px] font-semibold text-[#1d2327] mb-1">Liên kết (URL)</label>
                            <input v-model="form.link" type="url" placeholder="https://..." class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                            <div v-if="form.errors.link" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.link }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[13px] font-semibold text-[#1d2327] mb-1">Thứ tự hiển thị</label>
                                <input v-model="form.order" type="number" class="w-full border-[#8c8f94] rounded text-[13px] py-1.5 focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]" />
                            </div>
                            <div class="flex items-center pt-6">
                                <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-4 w-4" />
                                <label for="is_active" class="ml-2 text-[13px] text-[#50575e] cursor-pointer">Hoạt động</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="w-full lg:w-[300px] space-y-5">
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2 font-semibold text-[13px]">Hình ảnh Banner</div>
                        <div class="p-3 text-center">
                            <div v-if="imagePreview" class="mb-3">
                                <img :src="imagePreview" class="w-full h-auto rounded border border-[#c3c4c7]" />
                            </div>
                            <input type="file" id="image" @change="handleImageChange" class="hidden" accept="image/*" />
                            <label for="image" class="inline-block text-[13px] text-[#2271b1] hover:text-[#135e96] hover:underline cursor-pointer">
                                {{ imagePreview ? 'Thay đổi ảnh' : 'Chọn ảnh banner' }}
                            </label>
                            <div v-if="form.errors.image" class="text-[#d63638] text-[11px] mt-1">{{ form.errors.image }}</div>
                        </div>
                    </div>

                    <div class="bg-white border border-[#c3c4c7] shadow-sm p-3 flex justify-between">
                        <Link :href="route('admin.banners.index')" class="text-[13px] text-[#b32d2e] hover:underline pt-1.5">Hủy</Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-[#2271b1] hover:bg-[#135e96] text-white text-[13px] font-medium px-4 py-1.5 rounded-sm transition disabled:opacity-50"
                        >
                            {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật' : 'Thêm mới') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
