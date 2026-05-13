<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = computed(() => usePage().props.auth.user);
const userInitials = computed(() => {
    return user.value.name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
});
</script>

<template>
    <Head title="Hồ sơ cá nhân" />

    <AdminLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold leading-tight text-gray-800 dark:text-gray-100">
                Thiết lập tài khoản
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <!-- User Header Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="p-8 sm:flex sm:items-center sm:justify-between">
                        <div class="flex items-center space-x-5">
                            <div class="flex-shrink-0">
                                <div class="relative">
                                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-display font-bold shadow-lg">
                                        {{ userInitials }}
                                    </div>
                                    <span class="absolute bottom-0 right-0 block h-5 w-5 rounded-full bg-green-400 ring-4 ring-white dark:ring-gray-800" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-display font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                                <div class="mt-2 flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                        Quản trị viên
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 flex justify-center sm:mt-0">
                            <div class="flex space-x-3 text-sm text-gray-500 dark:text-gray-400">
                                <div class="text-center px-4 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                    <span class="block text-gray-900 dark:text-white font-bold">{{ new Date(user.created_at).toLocaleDateString('vi-VN') }}</span>
                                    Ngày tham gia
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Left Column: Navigation or Info -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                            <h3 class="font-display font-bold text-gray-900 dark:text-white mb-4">Hướng dẫn</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                Tại đây bạn có thể cập nhật thông tin cá nhân, thay đổi mật khẩu quản trị và quản lý các thiết lập bảo mật khác. Hãy đảm bảo thông tin của bạn luôn chính xác.
                            </p>
                        </div>
                        
                        <div class="bg-indigo-600 p-6 shadow-lg sm:rounded-2xl text-white">
                            <h3 class="font-display font-bold mb-2">Bảo mật tài khoản</h3>
                            <p class="text-xs opacity-80 mb-4">Sử dụng mật khẩu mạnh bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
                            <div class="h-1 w-full bg-white/20 rounded-full overflow-hidden">
                                <div class="h-full bg-white w-3/4"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Forms -->
                    <div class="md:col-span-2 space-y-8">
                        <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 dark:bg-gray-800">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </div>

                        <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 dark:bg-gray-800">
                            <UpdatePasswordForm />
                        </div>

                        <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-red-100 dark:border-red-900/20 dark:bg-gray-800">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
