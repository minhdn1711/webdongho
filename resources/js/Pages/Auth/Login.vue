<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Đăng nhập hệ thống" />

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-white">Chào mừng trở lại!</h2>
            <p class="mt-2 text-slate-400">Vui lòng đăng nhập để quản lý cửa hàng của bạn.</p>
        </div>

        <div v-if="status" class="mb-6 rounded-lg bg-green-500/10 p-4 text-sm font-medium text-green-400 border border-green-500/20">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Địa chỉ Email" class="text-slate-300" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.email"
                    placeholder="name@company.com"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Mật khẩu" class="text-slate-300" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-orange-500 hover:text-orange-400 transition-colors"
                    >
                        Quên mật khẩu?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" class="border-slate-700 text-orange-500 focus:ring-orange-500" />
                <span class="ms-2 text-sm text-slate-400">Ghi nhớ đăng nhập</span>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center bg-gradient-to-r from-orange-500 to-orange-600 py-3 text-base font-semibold hover:from-orange-600 hover:to-orange-700 focus:ring-orange-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Đăng nhập ngay
                </PrimaryButton>
            </div>

            <div class="text-center text-sm text-slate-500">
                Chưa có tài khoản? 
                <Link :href="route('register')" class="font-medium text-orange-500 hover:text-orange-400 transition-colors">
                    Đăng ký miễn phí
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
