<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import RichEditor from '@/Components/RichEditor.vue';
import MediaLibrary from '@/Components/MediaLibrary.vue';
import { ref } from 'vue';

const props = defineProps({
    post: Object
});

const form = useForm({
    title: props.post.title,
    content: props.post.content,
    image: props.post.image,
    is_published: props.post.is_published,
});

const showMediaLibrary = ref(false);

const submit = () => {
    form.put(route('admin.posts.update', props.post.id));
};

const handleImageSelect = (image) => {
    form.image = image.url;
    showMediaLibrary.value = false;
};
</script>

<template>
    <Head title="Chỉnh sửa bài viết" />

    <AdminLayout>
        <template #header>
            <h1 class="text-[23px] font-normal text-[#1d2327] leading-tight">
                Chỉnh sửa bài viết
            </h1>
        </template>

        <form @submit.prevent="submit">
            <div class="flex flex-col lg:flex-row gap-5">
                <!-- ===== LEFT COLUMN ===== -->
                <div class="flex-1 min-w-0 space-y-5">
                    <!-- Title -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="Nhập tiêu đề bài viết tại đây"
                            class="w-full border-0 border-b border-[#dcdcde] text-[#1d2327] text-lg py-3 px-3 focus:ring-0 focus:border-[#2271b1] focus:shadow-[0_0_0_1px_#2271b1] placeholder-[#a7aaad]"
                            required
                        />
                        <div v-if="form.errors.title" class="text-[#d63638] text-xs px-3 py-1 bg-[#fcf0f1]">{{ form.errors.title }}</div>
                    </div>

                    <!-- Content Editor -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Nội dung bài viết</span>
                        </div>
                        <div class="p-0">
                            <RichEditor
                                v-model="form.content"
                                placeholder="Viết nội dung bài viết ở đây..."
                                height="400px"
                            />
                        </div>
                        <div v-if="form.errors.content" class="text-[#d63638] text-xs px-3 py-1 bg-[#fcf0f1]">{{ form.errors.content }}</div>
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
                                Trạng thái: <strong class="text-[#1d2327]">{{ post.is_published ? 'Đã xuất bản' : 'Bản nháp' }}</strong>
                            </div>
                            <div class="flex items-center gap-2 text-[13px] text-[#50575e]">
                                <svg class="w-4 h-4 text-[#8c8f94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Ngày tạo: <strong class="text-[#1d2327]">{{ new Date(post.created_at).toLocaleDateString('vi-VN') }}</strong>
                            </div>
                            <div class="flex items-center">
                                <input v-model="form.is_published" type="checkbox" id="published" class="rounded border-[#8c8f94] text-[#2271b1] focus:ring-[#2271b1] h-4 w-4" />
                                <label for="published" class="ml-2 text-[13px] text-[#50575e] cursor-pointer">Công khai bài viết</label>
                            </div>
                        </div>
                        <div class="border-t border-[#c3c4c7] bg-[#f6f7f7] px-3 py-2 flex justify-between items-center">
                            <Link :href="route('admin.posts.index')" class="text-[13px] text-[#b32d2e] hover:text-[#a02929] hover:underline">
                                Quay lại
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-[#2271b1] hover:bg-[#135e96] text-white text-[13px] font-medium px-4 py-1.5 rounded-sm border border-[#2271b1] transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Đang lưu...' : 'Cập nhật' }}
                            </button>
                        </div>
                    </div>

                    <!-- Featured Image Box -->
                    <div class="bg-white border border-[#c3c4c7] shadow-sm">
                        <div class="bg-[#f0f0f1] border-b border-[#c3c4c7] px-3 py-2">
                            <span class="text-[13px] font-semibold text-[#1d2327]">Ảnh đại diện</span>
                        </div>
                        <div class="p-3">
                            <div v-if="form.image" class="mb-3 relative">
                                <img :src="form.image" class="w-full h-auto rounded border border-[#c3c4c7] object-cover max-h-[200px]" />
                                <button
                                    @click.prevent="form.image = ''"
                                    type="button"
                                    class="absolute top-1 right-1 bg-white/90 text-[#b32d2e] rounded-full p-1 shadow-sm hover:bg-red-50 transition border border-[#c3c4c7]"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div>
                                <button type="button" @click="showMediaLibrary = true" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-[13px] font-medium text-[#2271b1] border border-dashed border-[#2271b1] rounded hover:bg-[#f0f6fc]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    {{ form.image ? 'Chọn ảnh khác' : 'Chọn ảnh từ thư viện' }}
                                </button>
                                <p class="text-[11px] text-[#8c8f94] mt-1.5">Chọn ảnh đã tải lên trong mục Hình ảnh.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <MediaLibrary :show="showMediaLibrary" @close="showMediaLibrary = false" @select="handleImageSelect" />
    </AdminLayout>
</template>
