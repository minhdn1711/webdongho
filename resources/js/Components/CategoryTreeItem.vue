<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    nodes: { type: Array, default: () => [] },
    activeId: { type: Number, default: null },
    depth: { type: Number, default: 0 },
});
</script>

<template>
    <ul :class="depth > 0 ? 'mt-2 ml-3 pl-3 border-l border-gray-200 space-y-2' : 'space-y-2'">
        <li v-for="cat in nodes" :key="cat.id">
            <Link
                :href="route('category.show', cat.slug)"
                :class="activeId === cat.id ? 'text-[#d10000] font-bold' : 'text-gray-600 hover:text-[#d10000]'"
            >{{ cat.name }}</Link>
            <CategoryTreeItem
                v-if="cat.children?.length"
                :nodes="cat.children"
                :active-id="activeId"
                :depth="depth + 1"
            />
        </li>
    </ul>
</template>
