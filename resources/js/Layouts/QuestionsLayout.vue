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
});

</script>

<template>
   <div>

        <Head :title="title" />
        <Banner />

        <div class="min-h-screen bg-triviusBlue">
            <!-- Page Heading -->
            <header v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 text-blue-50">
                    <h2 class="font-semibold text-xl">
                        <slot name="header" />
                    </h2>
                </div>
            </header>

            <!-- Page Content -->
            <main class="w-full">
                <div class="max-w-7xl mx-auto px-5 py-6 sm:px-6 lg:px-10 text-indigo-900 bg-blue-50 shadow-md overflow-hidden sm:rounded-lg">
                    <slot />
                </div>
            </main>
        </div>
   </div>
</template>
