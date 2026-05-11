<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-slate-950 py-6 sm:py-12"
    >
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-orange-500/20 blur-[120px]"></div>
            <div class="absolute -bottom-[10%] -right-[10%] h-[40%] w-[40%] rounded-full bg-indigo-500/20 blur-[120px]"></div>
        </div>

        <div class="relative z-10">
            <Link href="/" class="flex flex-col items-center gap-4 transition-transform hover:scale-105">
                <ApplicationLogo class="h-20 w-20 fill-current text-orange-500" />
                <h1 class="text-2xl font-bold tracking-wider text-white uppercase">DONGHO<span class="text-orange-500">WEB</span></h1>
            </Link>
        </div>

        <div
            class="relative z-10 mt-8 w-full border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-2xl sm:max-w-md sm:rounded-3xl"
        >
            <!-- Decorative Inner Glow -->
            <div class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-orange-500/50 to-transparent"></div>
            
            <slot />
        </div>
        
        <div class="relative z-10 mt-8 text-center">
            <p class="text-sm text-slate-500">© 2024 DongHoWeb. All rights reserved.</p>
        </div>
    </div>
</template>
