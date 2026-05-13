<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-xl font-display font-bold text-red-600 dark:text-red-400">
                Xóa tài khoản
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Một khi tài khoản của bạn bị xóa, tất cả các tài nguyên và dữ liệu liên quan sẽ bị xóa vĩnh viễn. Hãy tải xuống bất kỳ dữ liệu nào bạn muốn giữ lại trước khi thực hiện.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Xóa tài khoản vĩnh viễn</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">
                    Bạn có chắc chắn muốn xóa tài khoản?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Hành động này không thể hoàn tác. Vui lòng nhập mật khẩu của bạn để xác nhận rằng bạn muốn xóa vĩnh viễn tài khoản của mình.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Mật khẩu"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Nhập mật khẩu để xác nhận"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Hủy bỏ </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Xác nhận xóa tài khoản
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
