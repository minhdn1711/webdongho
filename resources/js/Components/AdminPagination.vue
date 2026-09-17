<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    paginator: { type: Object, required: true },
    label: { type: String, default: 'mục' },
});
</script>

<template>
    <div v-if="paginator.last_page > 1" class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <p class="text-[13px] text-[#50575e]">
            Hiển thị {{ paginator.from }}–{{ paginator.to }} / {{ paginator.total }} {{ label }}
        </p>
        <div class="flex flex-wrap items-center gap-1">
            <template v-for="link in paginator.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    v-html="link.label"
                    preserve-scroll
                    class="px-3 py-1 text-[13px] border rounded transition"
                    :class="link.active
                        ? 'bg-[#2271b1] text-white border-[#2271b1]'
                        : 'bg-white text-[#2271b1] border-[#c3c4c7] hover:bg-[#f0f0f1]'"
                />
                <span
                    v-else
                    v-html="link.label"
                    class="px-3 py-1 text-[13px] border border-[#c3c4c7] rounded text-[#8c8f94] bg-[#f6f7f7]"
                />
            </template>
        </div>
    </div>
</template>
