<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-900">Quản lý Thuộc tính</h1>
      <Link href="/admin/attributes/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        Thêm Thuộc tính
      </Link>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="attribute in attributes.data" :key="attribute.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ attribute.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ attribute.slug }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">
              <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                {{ attribute.type }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm space-x-3">
              <Link :href="`/admin/attributes/${attribute.id}/values`" class="text-green-600 hover:text-green-900">
                Giá trị
              </Link>
              <Link :href="`/admin/attributes/${attribute.id}/edit`" class="text-blue-600 hover:text-blue-900">
                Sửa
              </Link>
              <button
                @click="deleteAttribute(attribute.id)"
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

defineProps({
  attributes: Object,
});

const deleteAttribute = (id) => {
  if (confirm('Bạn có chắc chắn muốn xóa thuộc tính này?')) {
    router.delete(`/admin/attributes/${id}`);
  }
};
</script>
