<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    posts: Array
});

const postToDelete = ref(null);

const confirmDelete = (post) => {
    postToDelete.value = post;
};

const deletePost = () => {
    router.delete(route('admin.posts.destroy', postToDelete.value.id), {
        onSuccess: () => {
            postToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Quản lý tin tức" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h1 class="text-[23px] font-normal text-[#1d2327]">
                    Bài viết
                    <Link :href="route('admin.posts.create')" class="text-[13px] font-medium text-[#2271b1] hover:text-[#135e96] ml-2 relative top-[-2px]">
                        Viết bài mới
                    </Link>
                </h1>
            </div>
        </template>

        <div class="bg-white border border-[#c3c4c7] shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Hình ảnh</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Tiêu đề</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Trạng thái</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Ngày đăng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="post in posts" :key="post.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] transition group">
                        <td class="px-3 py-2">
                            <img :src="post.image || 'https://via.placeholder.com/150'" class="w-16 h-10 object-cover rounded border border-[#c3c4c7]" />
                        </td>
                        <td class="px-3 py-2">
                            <Link :href="route('admin.posts.edit', post.id)" class="text-[13px] font-semibold text-[#2271b1] hover:text-[#135e96] hover:underline">
                                {{ post.title }}
                            </Link>
                            <!-- Row Actions (WordPress style) -->
                            <div class="text-[12px] mt-0.5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-[#8c8f94]">
                                <Link :href="route('admin.posts.edit', post.id)" class="text-[#2271b1] hover:text-[#135e96]">Sửa</Link>
                                <span>|</span>
                                <button @click="confirmDelete(post)" class="text-[#b32d2e] hover:text-[#a02929]">Xóa</button>
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <span
                                :class="post.is_published ? 'text-[#00a32a]' : 'text-[#8c8f94]'"
                                class="text-[13px]"
                            >
                                {{ post.is_published ? 'Đã xuất bản' : 'Bản nháp' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">
                            {{ new Date(post.created_at).toLocaleDateString('vi-VN') }}
                        </td>
                    </tr>
                    <tr v-if="!posts.length">
                        <td colspan="4" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có bài viết nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="postToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold mb-2 text-[#1d2327]">Xác nhận xóa</h3>
                <p class="text-[#50575e] text-[14px] mb-6">Bạn có chắc chắn muốn xóa bài viết <strong>"{{ postToDelete.title }}"</strong>? Hành động này không thể hoàn tác.</p>
                <div class="flex justify-end gap-3">
                    <button @click="postToDelete = null" class="px-4 py-2 text-[13px] font-medium text-[#50575e] bg-[#f0f0f1] hover:bg-[#dcdcde] rounded transition">
                        Hủy
                    </button>
                    <button @click="deletePost" class="px-4 py-2 text-[13px] font-medium text-white bg-[#d63638] hover:bg-[#b32d2e] rounded transition">
                        Xóa ngay
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
