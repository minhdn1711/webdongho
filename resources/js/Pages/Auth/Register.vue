<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

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

                <div class="relative mt-1">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full bg-slate-900/50 border-slate-700 text-white focus:border-orange-500 focus:ring-orange-500 pr-10"
                        v-model="form.password"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Xác nhận mật khẩu"
                    class="text-slate-300"
                />

                <div class="relative mt-1">
                    <TextInput
                        id="password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        class="block w-full bg-slate-900/50 border-slate-700 text-white focus:border-orange-500 focus:ring-orange-500 pr-10"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    />
                    <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                        <svg v-if="showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

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
