<script setup>

import Banner from "@/Components/Banner.vue";
import {Head} from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen flex flex-col">
<!--        <Head :title="title" />-->
<!--        <Banner />-->

        <!-- Header section with its own background image -->
        <div class="w-full bg-cover bg-center bg-no-repeat text-black" style="background-image: url('/images/triviya-bg-cover.png');">
            <header v-if="$slots.header" class="max-w-7xl mx-auto py-6">
                <h2 class="font-semibold text-xl text-black-900">
                    <slot name="header" />
                </h2>
            </header>
        </div>
        <!-- Main section with a separate background image -->
        <div class="flex-grow w-full bg-cover bg-center bg-no-repeat text-black relative" style="background-image: url('/images/triviya-bg-cover.png');">
            <!-- Gradient overlay at the top using the image (rotated 180 degrees) -->
            <div class="absolute top-0 left-0 right-0 h-[51px] bg-no-repeat bg-top bg-cover pointer-events-none" style="background-image: url('/images/gradient.png'); transform: rotate(180deg);"></div>

            <!-- Gradient overlay at the bottom using the image -->
            <div class="absolute bottom-0 left-0 right-0 h-[51px] bg-no-repeat bg-bottom bg-cover pointer-events-none" style="background-image: url('/images/gradient.png');"></div>

            <main class="w-full relative z-10 overflow-hidden">
                <div class="max-w-7xl mx-auto px-5">
                    <slot name="bubbles" />
                </div>
            </main>
        </div>

        <!-- Bottom button bar (fixed to bottom on mobile) -->
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-triviya-darkPurple p-6 flex justify-center"
            style="background-image: linear-gradient(to bottom, #A93390 3px, transparent 3px);">
            <slot name="question-buttons" />
        </div>

    </div>
</template>

<style scoped>

</style>
