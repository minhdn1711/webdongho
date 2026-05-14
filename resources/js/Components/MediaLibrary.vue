<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Thư viện hình ảnh'
    }
});

const emit = defineEmits(['close', 'select']);

const images = ref([]);
const loading = ref(false);
const uploading = ref(false);
const searchQuery = ref('');
const selectedImage = ref(null);

const fetchImages = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('admin.images.index'), {
            headers: { 'Accept': 'application/json' }
        });
        images.value = response.data.images;
    } catch (error) {
        console.error('Lỗi khi tải thư viện ảnh:', error);
    } finally {
        loading.value = false;
    }
};

const handleFileUpload = async (e) => {
    const files = e.target.files;
    if (!files.length) return;

    uploading.value = true;
    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
    }

    try {
        await axios.post(route('admin.images.store'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        await fetchImages(); // Tải lại danh sách sau khi upload
    } catch (error) {
        alert('Lỗi khi tải ảnh lên. Vui lòng thử lại.');
    } finally {
        uploading.value = false;
    }
};

const selectImage = (image) => {
    selectedImage.value = image;
};

const confirmSelection = () => {
    if (selectedImage.value) {
        emit('select', selectedImage.value);
        emit('close');
    }
};

onMounted(() => {
    fetchImages();
});

const filteredImages = () => {
    if (!searchQuery.value) return images.value;
    return images.value.filter(img => 
        img.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-[#f8f9fa]">
                <h3 class="text-lg font-bold text-gray-800">{{ title }}</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Toolbar -->
            <div class="px-6 py-3 border-b border-gray-200 flex flex-wrap gap-4 items-center bg-white">
                <div class="flex-1 min-w-[200px]">
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Tìm kiếm ảnh theo tên..." 
                        class="w-full border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <input 
                        type="file" 
                        id="media-upload" 
                        multiple 
                        class="hidden" 
                        @change="handleFileUpload" 
                        accept="image/*"
                    />
                    <label 
                        for="media-upload" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium cursor-pointer transition flex items-center gap-2"
                        :class="{'opacity-50 pointer-events-none': uploading}"
                    >
                        <span v-if="uploading" class="animate-spin">⌛</span>
                        <span>{{ uploading ? 'Đang tải lên...' : 'Tải ảnh mới' }}</span>
                    </label>
                    <button @click="fetchImages" class="p-2 text-gray-500 hover:bg-gray-100 rounded transition" title="Làm mới">
                        <svg class="w-5 h-5" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m0 0H15" /></svg>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <div v-if="loading" class="flex justify-center items-center h-full">
                    <div class="animate-pulse flex flex-col items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full mb-2"></div>
                        <div class="h-4 w-24 bg-gray-300 rounded"></div>
                    </div>
                </div>

                <div v-else-if="filteredImages().length === 0" class="flex flex-col items-center justify-center h-full text-gray-400">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p>Không tìm thấy hình ảnh nào.</p>
                </div>

                <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div 
                        v-for="img in filteredImages()" 
                        :key="img.path" 
                        @click="selectImage(img)"
                        class="relative aspect-square bg-white border-2 rounded-lg overflow-hidden cursor-pointer transition group"
                        :class="selectedImage?.path === img.path ? 'border-blue-500 ring-2 ring-blue-200' : 'border-transparent hover:border-gray-300 shadow-sm'"
                    >
                        <img :src="img.url" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[10px] px-1 py-0.5 truncate opacity-0 group-hover:opacity-100 transition">
                            {{ img.name }}
                        </div>
                        <!-- Checkmark for selected -->
                        <div v-if="selectedImage?.path === img.path" class="absolute top-1 right-1 bg-blue-500 text-white rounded-full p-0.5 shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-white flex justify-between items-center">
                <div class="text-xs text-gray-500">
                    <span v-if="selectedImage">Đã chọn: <strong class="text-gray-700">{{ selectedImage.name }}</strong></span>
                    <span v-else>Vui lòng chọn một hình ảnh</span>
                </div>
                <div class="flex gap-3">
                    <button @click="$emit('close')" class="px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 border border-gray-300 rounded transition">Hủy</button>
                    <button 
                        @click="confirmSelection" 
                        :disabled="!selectedImage"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded transition disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
                    >
                        Sử dụng ảnh này
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-in {
    animation: animate-in 0.2s ease-out;
}
@keyframes animate-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>
