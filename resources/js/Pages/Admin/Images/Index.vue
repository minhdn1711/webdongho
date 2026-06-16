<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    images: Array
});

const IMAGES_PER_PAGE = 30; // 5 hàng × 6 cột
const visibleCount = ref(IMAGES_PER_PAGE);

const visibleImages = computed(() => props.images.slice(0, visibleCount.value));
const hasMore = computed(() => visibleCount.value < props.images.length);

const loadMore = () => {
    visibleCount.value += IMAGES_PER_PAGE;
};

const uploadForm = useForm({
    images: []
});

const isUploading = ref(false);

const handleFileUpload = (e) => {
    uploadForm.images = e.target.files;
};

const submitUpload = () => {
    isUploading.value = true;
    uploadForm.post(route('admin.images.store'), {
        onSuccess: () => {
            uploadForm.reset();
            isUploading.value = false;
        },
        onError: () => {
            isUploading.value = false;
        }
    });
};

const deleteImage = (path) => {
    if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
        router.delete(route('admin.images.destroy'), {
            data: { path: path }
        });
    }
};

const copyToClipboard = (url) => {
    navigator.clipboard.writeText(url);
    alert('Đã copy đường dẫn ảnh!');
};
</script>

<template>
    <Head title="Quản lý hình ảnh" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thư viện hình ảnh</h2>
        </template>

        <div class="py-6 px-4 md:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Upload Section -->
                <div class="mb-10 border-b pb-10">
                    <h3 class="text-lg font-bold mb-4">Tải lên hình ảnh mới</h3>
                    <form @submit.prevent="submitUpload" class="flex flex-col md:flex-row items-end gap-4">
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ảnh (Có thể chọn nhiều)</label>
                            <input 
                                type="file" 
                                multiple 
                                @change="handleFileUpload"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            />
                        </div>
                        <button 
                            type="submit" 
                            :disabled="uploadForm.images.length === 0 || isUploading"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
                        >
                            <svg v-if="isUploading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ isUploading ? 'Đang tải lên...' : 'Tải lên ngay' }}
                        </button>
                    </form>
                </div>

                <!-- Grid Section -->
                <div>
                    <h3 class="text-lg font-bold mb-6">Tất cả hình ảnh ({{ images.length }})</h3>
                    
                    <div v-if="images.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                        <div v-for="image in visibleImages" :key="image.path" class="group relative bg-gray-50 rounded-xl overflow-hidden border hover:border-blue-400 transition shadow-sm">
                            <div class="aspect-square relative">
                                <img :src="image.url" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                    <button @click="copyToClipboard(image.url)" class="p-2 bg-white rounded-full text-blue-600 hover:bg-blue-50 shadow-lg" title="Copy Link">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    </button>
                                    <button @click="deleteImage(image.path)" class="p-2 bg-white rounded-full text-red-600 hover:bg-red-50 shadow-lg" title="Xóa ảnh">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="p-2 bg-white border-t">
                                <p class="text-[10px] text-gray-500 truncate" :title="image.name">{{ image.name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nút Xem thêm -->
                    <div v-if="hasMore" class="mt-8 flex justify-center">
                        <button
                            @click="loadMore"
                            class="px-8 py-3 bg-white border-2 border-blue-500 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition"
                        >
                            Xem thêm ({{ images.length - visibleCount }} ảnh còn lại)
                        </button>
                    </div>

                    <div v-if="images.length === 0" class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed">
                        <p class="text-gray-400">Thư viện ảnh trống. Hãy tải lên ảnh đầu tiên của bạn!</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
