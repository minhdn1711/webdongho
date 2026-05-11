<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    posts: Array
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Tin tức & Sự kiện - Julius Việt Nam" />

    <ClientLayout>
        <div class="bg-gray-50 py-10 md:py-20">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h1 class="text-xs md:text-sm font-bold text-[#d10000] uppercase tracking-[0.4em] mb-4">Blog</h1>
                    <h2 class="text-3xl md:text-5xl font-bold uppercase italic">Tin tức & Sự kiện</h2>
                    <div class="w-20 h-1 bg-[#d10000] mx-auto mt-6"></div>
                </div>

                <div v-if="posts.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                    <Link v-for="post in posts" :key="post.id" :href="route('news.show', post.slug)" class="bg-white group cursor-pointer shadow-sm hover:shadow-xl transition flex flex-col">
                        <div class="overflow-hidden aspect-[16/9]">
                            <img :src="post.image || 'https://via.placeholder.com/800x450'" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <span>{{ formatDate(post.created_at) }}</span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span>Julius Team</span>
                            </div>
                            <h3 class="text-lg font-bold mb-4 group-hover:text-[#d10000] transition line-clamp-2">{{ post.title }}</h3>
                            <p class="text-sm text-gray-500 line-clamp-3 mb-6 flex-1">
                                {{ post.content.substring(0, 150) }}...
                            </p>
                            <span class="text-xs font-bold uppercase tracking-widest text-[#d10000] flex items-center gap-2">
                                Đọc thêm
                                <svg class="w-3 h-3 translate-x-0 group-hover:translate-x-2 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </span>
                        </div>
                    </Link>
                </div>

                <div v-else class="text-center py-20 bg-white border-2 border-dashed rounded-xl">
                    <p class="text-gray-400 italic">Chưa có bài viết nào được cập nhật.</p>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
