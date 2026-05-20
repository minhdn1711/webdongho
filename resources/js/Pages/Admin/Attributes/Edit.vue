<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">Sửa Thuộc tính</h1>

    <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tên Thuộc tính</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
            placeholder="e.g., Màu sắc"
          />
          <span v-if="errors.name" class="text-red-600 text-sm">{{ errors.name }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
          <input
            v-model="form.slug"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
            placeholder="e.g., color"
          />
          <span v-if="errors.slug" class="text-red-600 text-sm">{{ errors.slug }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Loại</label>
          <select
            v-model="form.type"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
          >
            <option value="">Chọn loại</option>
            <option value="text">Text</option>
            <option value="color">Color</option>
            <option value="button">Button</option>
          </select>
          <span v-if="errors.type" class="text-red-600 text-sm">{{ errors.type }}</span>
        </div>

        <div class="flex space-x-3">
          <button
            type="submit"
            :disabled="processing"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            {{ processing ? 'Đang xử lý...' : 'Cập nhật' }}
          </button>
          <Link href="/admin/attributes" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Hủy
          </Link>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  attribute: Object,
});

const form = useForm({
  name: '',
  slug: '',
  type: '',
});

form.name = attribute.name;
form.slug = attribute.slug;
form.type = attribute.type;

const errors = ref({});
const processing = ref(false);

const submit = async () => {
  processing.value = true;
  form.patch(`/admin/attributes/${attribute.id}`);
};
</script>
