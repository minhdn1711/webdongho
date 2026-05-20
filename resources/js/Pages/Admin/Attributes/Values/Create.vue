<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">Thêm Giá trị - {{ props.attribute.name }}</h1>

    <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Giá trị</label>
          <input
            v-model="form.value"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
            placeholder="e.g., Đỏ"
          />
          <span v-if="form.errors.value" class="text-red-600 text-sm">{{ form.errors.value }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
          <input
            v-model="form.slug"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
            placeholder="e.g., red"
          />
          <span v-if="form.errors.slug" class="text-red-600 text-sm">{{ form.errors.slug }}</span>
        </div>

        <div v-if="props.attribute.type === 'color'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Mã Màu (Hex)</label>
          <div class="flex space-x-2">
            <input
              v-model="form.meta_value"
              type="color"
              class="w-20 h-10 border border-gray-300 rounded-lg cursor-pointer"
            />
            <input
              v-model="form.meta_value"
              type="text"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              placeholder="e.g., #FF0000"
            />
          </div>
          <span v-if="form.errors.meta_value" class="text-red-600 text-sm">{{ form.errors.meta_value }}</span>
        </div>

        <div class="flex space-x-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            {{ form.processing ? 'Đang xử lý...' : 'Tạo' }}
          </button>
          <Link :href="`/admin/attributes/${props.attribute.id}/values`" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Hủy
          </Link>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  attribute: Object,
});

const form = useForm({
  value: '',
  slug: '',
  meta_value: '#000000',
});

const submit = () => {
  form.post(`/admin/attributes/${props.attribute.id}/values`);
};
</script>
