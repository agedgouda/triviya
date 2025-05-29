<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import NavDropdown from '@/Components/NavDropdown.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
    headerHasBackground: {
        type: Boolean,
        default: true,
    },
});


</script>

<template>
    <div class="min-h-screen flex flex-col">
        <Head :title="title" />
        <Banner />

        <!-- Header section with its own background image -->
        <div v-if="$slots.header" class="w-full bg-cover bg-center bg-no-repeat text-black" style="background-image: url('/images/triviya-bg-cover.png');">
            <header class="max-w-7xl mx-auto py-6">
                <h2 class="font-semibold text-xl text-black-900">
                    <slot name="header" />
                </h2>
            </header>
        </div>



        <!-- Main section with a separate background image -->
        <div class="flex-grow w-full bg-cover bg-center bg-no-repeat text-black relative" style="background-image: url('/images/triviya-bg-cover.png');">
        <!-- Gradient overlay at the top using the image (rotated 180 degrees) -->
        <div class="absolute top-0 left-0 right-0 h-[51px] bg-no-repeat bg-top bg-cover pointer-events-none" style="background-image: url('/images/gradient.png'); transform: rotate(180deg);"></div>

            <div v-if="$slots['raw-header']">
                <slot name="raw-header" />
            </div>


        <!-- Gradient overlay at the bottom using the image -->
        <div class="absolute bottom-0 left-0 right-0 h-[51px] bg-no-repeat bg-bottom bg-cover pointer-events-none" style="background-image: url('/images/gradient.png');"></div>

        <main class="w-full relative z-10">
            <div class="max-w-7xl mx-auto px-5">

                <!-- Question Header (purple background) -->
                <div
                    v-if="$slots['question-header']"

                    :class="[
                        headerHasBackground ? 'bg-triviya-darkPurple shadow-md overflow-hidden sm:rounded-lg' : '',
                        'p-6 mt-12 mb-6 text-white mx-auto max-w-xl rounded-xl'
                    ]"
                    >
                    <slot name="question-header" />
                </div>

                <!-- Question Input (white background) -->
                <div v-if="$slots['question-input']" class="bg-white shadow-md sm:rounded-lg p-6 mb-6 mx-auto max-w-xl rounded-xl">
                    <slot name="question-input" />
                </div>

                <div v-if="$slots['question-footer']" class="p-6 mb-6 mx-auto text-white text-lg text-center max-w-xl rounded-xl">
                    <slot name="question-footer" />
                </div>

            </div>
        </main>
    </div>


    <!-- Bottom button bar (dark purple background) -->
    <div class="w-full bg-triviya-darkPurple p-6 flex justify-center" style="background-image: linear-gradient(to bottom, #A93390 3px, transparent 3px);">
        <slot name="question-buttons" />
    </div>


    </div>
</template>



