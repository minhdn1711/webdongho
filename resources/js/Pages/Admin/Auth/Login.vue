<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
    error: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('admin.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Đăng nhập Quản trị" />

    <div class="min-h-screen flex items-center justify-center bg-slate-900">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Quản trị hệ thống</h1>
                <p class="mt-1 text-slate-400 text-sm">Chỉ dành cho quản trị viên</p>
            </div>

            <div class="bg-slate-800 rounded-2xl shadow-xl p-8 border border-slate-700">
                <div v-if="status" class="mb-4 rounded-lg bg-green-500/10 p-3 text-sm text-green-400 border border-green-500/20">
                    {{ status }}
                </div>
                <div v-if="error" class="mb-4 rounded-lg bg-red-500/10 p-3 text-sm text-red-400 border border-red-500/20">
                    {{ error }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            placeholder="admin@example.com"
                        />
                        <InputError class="mt-1.5" :message="form.errors.email" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Mật khẩu</label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            placeholder="••••••••"
                        />
                        <InputError class="mt-1.5" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="rounded border-slate-600 text-orange-500 focus:ring-orange-500 bg-slate-900"
                        />
                        <label for="remember" class="ml-2 text-sm text-slate-400">Ghi nhớ đăng nhập</label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-2.5 px-4 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold rounded-lg transition-all disabled:opacity-50"
                    >
                        {{ form.processing ? 'Đang xử lý...' : 'Đăng nhập' }}
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-slate-600">
                <a href="/" class="hover:text-slate-400 transition-colors">← Về trang chủ</a>
            </p>
        </div>
    </div>
</template>
