<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    post: Object,
    recent_posts: Array
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
    <Head :title="post.title + ' - Lamtime Shop'" />

    <ClientLayout>
        <div class="bg-white py-10 md:py-20">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Main Content -->
                    <article class="flex-1">
                        <div class="text-[10px] md:text-xs font-bold text-[#d10000] uppercase tracking-[0.3em] mb-4">Tin tức & Sự kiện</div>
                        <h1 class="text-3xl md:text-5xl font-bold mb-6 italic leading-tight">{{ post.title }}</h1>
                        
                        <div class="flex items-center gap-4 text-xs text-gray-400 mb-10 border-b pb-6">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" /></svg>
                                <span>{{ formatDate(post.created_at) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span>Bởi: Miliwebseo Team</span>
                            </div>
                        </div>

                        <img v-if="post.image" :src="post.image" class="w-full aspect-[21/9] object-cover mb-10 shadow-lg" />

                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ post.content }}
                        </div>

                        <!-- Footer actions -->
                        <div class="mt-16 pt-8 border-t flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-bold uppercase tracking-widest text-gray-400">Chia sẻ:</span>
                                <div class="flex gap-2">
                                    <button class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:opacity-80 transition">f</button>
                                    <button class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center hover:opacity-80 transition">𝕏</button>
                                </div>
                            </div>
                            <Link :href="route('news.index')" class="text-[#d10000] font-bold uppercase tracking-widest text-sm flex items-center gap-2 group">
                                <svg class="w-4 h-4 -translate-x-0 group-hover:-translate-x-2 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                                Quay lại danh sách
                            </Link>
                        </div>
                    </article>

                    <!-- Sidebar -->
                    <aside class="w-full lg:w-80 shrink-0">
                        <div class="bg-gray-50 p-8 sticky top-32">
                            <h3 class="text-sm font-bold uppercase tracking-widest mb-6 border-b pb-2 text-[#d10000]">Bài viết mới nhất</h3>
                            <div class="space-y-6">
                                <Link v-for="recent in recent_posts" :key="recent.id" :href="route('news.show', recent.slug)" class="group block">
                                    <div class="text-[10px] text-gray-400 mb-1">{{ formatDate(recent.created_at) }}</div>
                                    <h4 class="text-sm font-bold group-hover:text-[#d10000] transition line-clamp-2 leading-snug">{{ recent.title }}</h4>
                                </Link>
                            </div>

                            <div class="mt-12">
                                <div class="bg-[#d10000] p-6 text-white text-center">
                                    <h4 class="font-bold italic text-xl mb-2 italic">Ưu đãi độc quyền</h4>
                                    <p class="text-xs text-white/80 mb-4 italic">Đăng ký ngay để nhận thông báo về bộ sưu tập mới và khuyến mãi.</p>
                                    <input type="email" placeholder="Email của bạn" class="w-full bg-white/10 border-white/20 text-xs py-2 px-3 mb-2 focus:ring-white" />
                                    <button class="w-full bg-white text-[#d10000] font-bold text-xs py-2 uppercase tracking-widest">Gửi ngay</button>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
