<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    banners: Array,
});

const bannerToDelete = ref(null);

const confirmDelete = (banner) => {
    bannerToDelete.value = banner;
};

const deleteBanner = () => {
    router.delete(route('admin.banners.destroy', bannerToDelete.value.id), {
        onSuccess: () => {
            bannerToDelete.value = null;
        },
    });
};

const toggleActive = (banner) => {
    router.patch(route('admin.banners.update', banner.id), {
        is_active: !banner.is_active
    });
};
</script>

<template>
    <Head title="Quản lý Banner" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h1 class="text-[23px] font-normal text-[#1d2327]">
                    Banners
                    <Link :href="route('admin.banners.create')" class="text-[13px] font-medium text-[#2271b1] hover:text-[#135e96] ml-2 relative top-[-2px]">
                        Thêm mới
                    </Link>
                </h1>
            </div>
        </template>

        <div class="bg-white border border-[#c3c4c7] shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#c3c4c7]">
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327] w-32">Hình ảnh</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Tiêu đề</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327]">Liên kết</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327] w-20">Thứ tự</th>
                        <th class="px-3 py-2 text-[13px] font-semibold text-[#1d2327] w-24 text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="banner in banners" :key="banner.id" class="border-b border-[#f0f0f1] hover:bg-[#f6f7f7] transition group">
                        <td class="px-3 py-2">
                            <img :src="banner.image_url" class="w-24 h-12 object-cover rounded border border-[#c3c4c7]" />
                        </td>
                        <td class="px-3 py-2">
                            <Link :href="route('admin.banners.edit', banner.id)" class="text-[13px] font-semibold text-[#2271b1] hover:text-[#135e96] hover:underline">
                                {{ banner.title || '(Không có tiêu đề)' }}
                            </Link>
                            <!-- Row Actions -->
                            <div class="text-[12px] mt-0.5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-[#8c8f94]">
                                <Link :href="route('admin.banners.edit', banner.id)" class="text-[#2271b1] hover:text-[#135e96]">Sửa</Link>
                                <span>|</span>
                                <button @click="confirmDelete(banner)" class="text-[#b32d2e] hover:text-[#a02929]">Xóa</button>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">
                            <a :href="banner.link" target="_blank" class="hover:underline">{{ banner.link || '---' }}</a>
                        </td>
                        <td class="px-3 py-2 text-[13px] text-[#50575e]">
                            {{ banner.order }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button @click="toggleActive(banner)" :class="banner.is_active ? 'text-green-600' : 'text-gray-400'">
                                <span v-if="banner.is_active" class="text-[18px]">●</span>
                                <span v-else class="text-[18px]">○</span>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!banners.length">
                        <td colspan="5" class="px-3 py-6 text-center text-[13px] text-[#8c8f94]">Chưa có banner nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="bannerToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold mb-2 text-[#1d2327]">Xác nhận xóa</h3>
                <p class="text-[#50575e] text-[14px] mb-6">Bạn có chắc chắn muốn xóa banner này? Hành động này không thể hoàn tác.</p>
                <div class="flex justify-end gap-3">
                    <button @click="bannerToDelete = null" class="px-4 py-2 text-[13px] font-medium text-[#50575e] bg-[#f0f0f1] hover:bg-[#dcdcde] rounded transition">
                        Hủy
                    </button>
                    <button @click="deleteBanner" class="px-4 py-2 text-[13px] font-medium text-white bg-[#d63638] hover:bg-[#b32d2e] rounded transition">
                        Xóa ngay
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
