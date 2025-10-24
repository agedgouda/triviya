<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    policy: String,
});

function closePage() {
    window.close();
}
</script>

<template>
    <Head title="Privacy Policy" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="pt-4 bg-gray-100">

            <div
                class="fixed top-4 right-4 text-sm cursor-pointer inline-flex items-center space-x-1 p-2 bg-white rounded shadow-md hover:bg-gray-100 z-50"
                @click="closePage"
            >
                <span>Close</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </div>


            <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-4xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose  prose-sm" v-html="policy" />
            </div>
        </div>
    </div>
</template>
