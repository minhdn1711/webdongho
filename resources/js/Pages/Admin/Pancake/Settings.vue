<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    shop_id: props.settings.shop_id,
    warehouse_id: props.settings.warehouse_id,
    api_token: props.settings.api_token,
    api_url: props.settings.api_url,
    webhook_secret: props.settings.webhook_secret,
    sync_products: props.settings.sync_products === '1',
    sync_orders: props.settings.sync_orders === '1',
});

const submit = () => {
    form.post(route('admin.pancake.settings.update'));
};
</script>

<template>
    <Head title="Cấu hình Pancake POS" />

    <AdminLayout>
        <template #header>
            <h1 class="text-[23px] font-normal text-[#1d2327]">Cấu hình Pancake POS</h1>
        </template>

        <div class="max-w-4xl">
            <div class="bg-white border border-[#c3c4c7] shadow-sm p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-[14px] font-semibold text-[#1d2327] mb-1">Shop ID</label>
                        <input 
                            v-model="form.shop_id" 
                            type="text" 
                            class="w-full border-[#c3c4c7] rounded shadow-sm focus:border-[#2271b1] focus:ring-0 text-[14px]"
                            placeholder="Ví dụ: 1328943504"
                        />
                        <div v-if="form.errors.shop_id" class="text-red-500 text-xs mt-1">{{ form.errors.shop_id }}</div>
                    </div>

                    <div>
                        <label class="block text-[14px] font-semibold text-[#1d2327] mb-1">Warehouse ID (Mã kho hàng)</label>
                        <input 
                            v-model="form.warehouse_id" 
                            type="text" 
                            class="w-full border-[#c3c4c7] rounded shadow-sm focus:border-[#2271b1] focus:ring-0 text-[14px]"
                            placeholder="Mã kho hàng từ Pancake POS"
                        />
                        <div v-if="form.errors.warehouse_id" class="text-red-500 text-xs mt-1">{{ form.errors.warehouse_id }}</div>
                    </div>

                    <div>
                        <label class="block text-[14px] font-semibold text-[#1d2327] mb-1">API Token</label>
                        <input 
                            v-model="form.api_token" 
                            type="password" 
                            class="w-full border-[#c3c4c7] rounded shadow-sm focus:border-[#2271b1] focus:ring-0 text-[14px]"
                            placeholder="Nhập API Token từ Pancake POS"
                        />
                        <div v-if="form.errors.api_token" class="text-red-500 text-xs mt-1">{{ form.errors.api_token }}</div>
                    </div>

                    <div>
                        <label class="block text-[14px] font-semibold text-[#1d2327] mb-1">API Base URL</label>
                        <input 
                            v-model="form.api_url" 
                            type="text" 
                            class="w-full border-[#c3c4c7] rounded shadow-sm focus:border-[#2271b1] focus:ring-0 text-[14px]"
                        />
                    </div>

                    <div>
                        <label class="block text-[14px] font-semibold text-[#1d2327] mb-1">Webhook Secret</label>
                        <input 
                            v-model="form.webhook_secret" 
                            type="text" 
                            class="w-full border-[#c3c4c7] rounded shadow-sm focus:border-[#2271b1] focus:ring-0 text-[14px]"
                            placeholder="Dùng để xác thực webhook gửi từ Pancake"
                        />
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-3">
                            <input 
                                v-model="form.sync_products" 
                                type="checkbox" 
                                id="sync_products"
                                class="rounded border-[#c3c4c7] text-[#2271b1] focus:ring-0"
                            />
                            <label for="sync_products" class="text-[14px] text-[#1d2327]">Tự động đồng bộ sản phẩm khi lưu</label>
                        </div>

                        <div class="flex items-center gap-3">
                            <input 
                                v-model="form.sync_orders" 
                                type="checkbox" 
                                id="sync_orders"
                                class="rounded border-[#c3c4c7] text-[#2271b1] focus:ring-0"
                            />
                            <label for="sync_orders" class="text-[14px] text-[#1d2327]">Tự động đồng bộ đơn hàng mới</label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#f0f0f1]">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="px-4 py-2 bg-[#2271b1] hover:bg-[#135e96] text-white text-[13px] font-semibold rounded transition disabled:opacity-50"
                        >
                            {{ form.processing ? 'Đang lưu...' : 'Lưu cấu hình' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
