<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <Link href="/admin/attributes" class="text-blue-600 hover:text-blue-900 text-sm">← Quay lại</Link>
        <h1 class="text-3xl font-bold text-gray-900">Giá trị - {{ attribute.name }}</h1>
      </div>
      <Link :href="`/admin/attributes/${attribute.id}/values/create`" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        Thêm Giá trị
      </Link>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giá trị</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
            <th v-if="attribute.type === 'color'" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Màu</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="value in values.data" :key="value.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ value.value }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ value.slug }}</td>
            <td v-if="attribute.type === 'color'" class="px-6 py-4 text-sm">
              <div v-if="value.meta_value" class="flex items-center space-x-2">
                <div :style="{ backgroundColor: value.meta_value }" class="w-8 h-8 border border-gray-300 rounded"></div>
                <span class="text-xs">{{ value.meta_value }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-sm space-x-3">
              <Link :href="`/admin/attributes/${attribute.id}/values/${value.id}/edit`" class="text-blue-600 hover:text-blue-900">
                Sửa
              </Link>
              <button
                @click="deleteValue(value.id)"
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  attribute: Object,
  values: Object,
});

const deleteValue = (id) => {
  if (confirm('Bạn có chắc chắn muốn xóa giá trị này?')) {
    router.delete(`/admin/attributes/${props.attribute.id}/values/${id}`);
  }
};
</script>
