<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Đăng ký tài khoản" />

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-white">Tạo tài khoản mới</h2>
            <p class="mt-2 text-slate-400">Tham gia cùng chúng tôi để bắt đầu mua sắm.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="name" value="Họ và tên" class="text-slate-300" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.name"
                    placeholder="Nguyễn Văn A"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Địa chỉ Email" class="text-slate-300" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.email"
                    placeholder="name@company.com"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Mật khẩu" class="text-slate-300" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.password"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Xác nhận mật khẩu"
                    class="text-slate-300"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-white focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.password_confirmation"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full justify-center bg-gradient-to-r from-orange-500 to-orange-600 py-3 text-base font-semibold hover:from-orange-600 hover:to-orange-700 focus:ring-orange-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Đăng ký ngay
                </PrimaryButton>
            </div>

            <div class="text-center text-sm text-slate-500">
                Đã có tài khoản? 
                <Link
                    :href="route('login')"
                    class="font-medium text-orange-500 hover:text-orange-400 transition-colors"
                >
                    Đăng nhập tại đây
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
