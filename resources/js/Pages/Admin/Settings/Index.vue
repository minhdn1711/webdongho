<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MediaLibrary from '@/Components/MediaLibrary.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    },
});

const form = useForm({
    site_name: (props.settings && props.settings.site_name) || 'Web Đồng Hồ',
    site_description: (props.settings && props.settings.site_description) || '',
    contact_email: (props.settings && props.settings.contact_email) || '',
    contact_phone: (props.settings && props.settings.contact_phone) || '',
    address: (props.settings && props.settings.address) || '',
    allow_out_of_stock_orders: (props.settings && props.settings.allow_out_of_stock_orders) || '0',
    low_stock_threshold: (props.settings && props.settings.low_stock_threshold) || '5',
    admin_notification_email: (props.settings && props.settings.admin_notification_email) || (props.settings && props.settings.contact_email) || '',
    logo: (props.settings && props.settings.logo) || null,
    favicon: (props.settings && props.settings.favicon) || null,
    contact_zalo: (props.settings && props.settings.contact_zalo) || '',
    contact_messenger: (props.settings && props.settings.contact_messenger) || '',
    contact_phone: (props.settings && props.settings.contact_phone) || '',
    contact_zalo_icon: (props.settings && props.settings.contact_zalo_icon) || null,
    contact_messenger_icon: (props.settings && props.settings.contact_messenger_icon) || null,
    contact_phone_icon: (props.settings && props.settings.contact_phone_icon) || null,
});

const logoPreview = ref((props.settings && props.settings.logo) || null);
const faviconPreview = ref((props.settings && props.settings.favicon) || null);
const zaloIconPreview = ref((props.settings && props.settings.contact_zalo_icon) || null);
const messengerIconPreview = ref((props.settings && props.settings.contact_messenger_icon) || null);
const phoneIconPreview = ref((props.settings && props.settings.contact_phone_icon) || null);
const showMediaLibrary = ref(false);
const activeField = ref(null);

const openMediaLibrary = (field) => {
    activeField.value = field;
    showMediaLibrary.value = true;
};

const handleImageSelect = (image) => {
    form[activeField.value] = image.url;
    if (activeField.value === 'logo') logoPreview.value = image.url;
    if (activeField.value === 'favicon') faviconPreview.value = image.url;
    if (activeField.value === 'contact_zalo_icon') zaloIconPreview.value = image.url;
    if (activeField.value === 'contact_messenger_icon') messengerIconPreview.value = image.url;
    if (activeField.value === 'contact_phone_icon') phoneIconPreview.value = image.url;
};

const handleFileUpload = (e, field) => {
    const file = e.target.files[0];
    if (file) {
        form[field] = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            if (field === 'logo') logoPreview.value = e.target.result;
            if (field === 'favicon') faviconPreview.value = e.target.result;
            if (field === 'contact_zalo_icon') zaloIconPreview.value = e.target.result;
            if (field === 'contact_messenger_icon') messengerIconPreview.value = e.target.result;
            if (field === 'contact_phone_icon') phoneIconPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = (field) => {
    form[field] = null;
    if (field === 'logo') logoPreview.value = null;
    if (field === 'favicon') faviconPreview.value = null;
    if (field === 'contact_zalo_icon') zaloIconPreview.value = null;
    if (field === 'contact_messenger_icon') messengerIconPreview.value = null;
    if (field === 'contact_phone_icon') phoneIconPreview.value = null;
};

const submit = () => {
    form.post(route('admin.settings.update'), {
        forceFormData: true,
        onSuccess: () => {
            // Success notification or logic
        },
    });
};
</script>

<template>
    <Head title="Cấu hình hệ thống" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cấu hình hệ thống</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Site Settings -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Thông tin chung</h3>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên trang web</label>
                                    <input v-model="form.site_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                                    <textarea v-model="form.site_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email liên hệ</label>
                                    <input v-model="form.contact_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                    <input v-model="form.contact_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                                    <textarea v-model="form.address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>

                            <!-- Media Settings -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Hình ảnh & Thương hiệu</h3>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                                    <div class="relative w-32 h-32 group">
                                        <div 
                                            @click="openMediaLibrary('logo')"
                                            class="w-full h-full border-2 border-dashed border-gray-300 rounded-lg overflow-hidden flex items-center justify-center bg-gray-50 cursor-pointer hover:border-[#2271b1] hover:bg-gray-100 transition"
                                        >
                                            <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-contain p-2" />
                                            <div v-else class="text-center">
                                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>
                                                <span class="text-[10px] text-gray-400">Chọn Logo</span>
                                            </div>
                                        </div>
                                        <button v-if="logoPreview" @click.prevent="removeImage('logo')" type="button" class="absolute -top-2 -right-2 bg-white text-red-600 rounded-full p-1 shadow-md hover:bg-red-50 border border-gray-200 z-10">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                                    <div class="relative w-16 h-16 group">
                                        <div 
                                            @click="openMediaLibrary('favicon')"
                                            class="w-full h-full border-2 border-dashed border-gray-300 rounded-lg overflow-hidden flex items-center justify-center bg-gray-50 cursor-pointer hover:border-[#2271b1] hover:bg-gray-100 transition"
                                        >
                                            <img v-if="faviconPreview" :src="faviconPreview" class="w-full h-full object-contain p-1" />
                                            <div v-else class="text-center">
                                                <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>
                                            </div>
                                        </div>
                                        <button v-if="faviconPreview" @click.prevent="removeImage('favicon')" type="button" class="absolute -top-2 -right-2 bg-white text-red-600 rounded-full p-1 shadow-md hover:bg-red-50 border border-gray-200 z-10">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Configuration -->
                            <div class="space-y-4 md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Cấu hình nút liên hệ (Zalo, Messenger, Hotlline)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Zalo</label>
                                        <input v-model="form.contact_zalo" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm mb-3" placeholder="Link Zalo..." />
                                        
                                        <div class="relative w-12 h-12 group">
                                            <div 
                                                @click="openMediaLibrary('contact_zalo_icon')"
                                                class="w-full h-full border-2 border-dashed border-gray-300 rounded-lg overflow-hidden flex items-center justify-center bg-gray-50 cursor-pointer hover:border-[#2271b1] hover:bg-gray-100 transition"
                                            >
                                                <img v-if="zaloIconPreview" :src="zaloIconPreview" class="w-full h-full object-contain" />
                                                <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>
                                            </div>
                                            <button v-if="zaloIconPreview" @click.prevent="removeImage('contact_zalo_icon')" type="button" class="absolute -top-2 -right-2 bg-white text-red-600 rounded-full p-1 shadow-md hover:bg-red-50 border border-gray-200 z-10">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Messenger</label>
                                        <input v-model="form.contact_messenger" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm mb-3" placeholder="Link Messenger..." />
                                        
                                        <div class="relative w-12 h-12 group">
                                            <div 
                                                @click="openMediaLibrary('contact_messenger_icon')"
                                                class="w-full h-full border-2 border-dashed border-gray-300 rounded-lg overflow-hidden flex items-center justify-center bg-gray-50 cursor-pointer hover:border-[#2271b1] hover:bg-gray-100 transition"
                                            >
                                                <img v-if="messengerIconPreview" :src="messengerIconPreview" class="w-full h-full object-contain" />
                                                <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>
                                            </div>
                                            <button v-if="messengerIconPreview" @click.prevent="removeImage('contact_messenger_icon')" type="button" class="absolute -top-2 -right-2 bg-white text-red-600 rounded-full p-1 shadow-md hover:bg-red-50 border border-gray-200 z-10">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Hotline (Nút gọi)</label>
                                        <input v-model="form.contact_phone" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm mb-3" placeholder="Số điện thoại..." />
                                        
                                        <div class="relative w-12 h-12 group">
                                            <div 
                                                @click="openMediaLibrary('contact_phone_icon')"
                                                class="w-full h-full border-2 border-dashed border-gray-300 rounded-lg overflow-hidden flex items-center justify-center bg-gray-50 cursor-pointer hover:border-[#2271b1] hover:bg-gray-100 transition"
                                            >
                                                <img v-if="phoneIconPreview" :src="phoneIconPreview" class="w-full h-full object-contain" />
                                                <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>
                                            </div>
                                            <button v-if="phoneIconPreview" @click.prevent="removeImage('contact_phone_icon')" type="button" class="absolute -top-2 -right-2 bg-white text-red-600 rounded-full p-1 shadow-md hover:bg-red-50 border border-gray-200 z-10">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory Settings -->
                            <div class="space-y-4 md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Quản lý kho hàng</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Cho phép đặt hàng khi hết hàng</label>
                                        <select v-model="form.allow_out_of_stock_orders" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="1">Bật (Khách vẫn có thể đặt khi kho = 0)</option>
                                            <option value="0">Tắt (Hiển thị hết hàng, không cho đặt)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Ngưỡng báo hàng tồn thấp</label>
                                        <input v-model="form.low_stock_threshold" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Ví dụ: 5" />
                                        <p class="mt-1 text-xs text-gray-500">Hệ thống sẽ gửi mail thông báo khi số lượng trong kho chạm ngưỡng này.</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email nhận thông báo kho</label>
                                        <input v-model="form.admin_notification_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="admin@example.com" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6">
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ form.processing ? 'Đang lưu...' : 'Lưu cấu hình' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>

    <!-- Media Library Modal -->
    <MediaLibrary 
        :show="showMediaLibrary" 
        @close="showMediaLibrary = false" 
        @select="handleImageSelect"
    />
</template>
