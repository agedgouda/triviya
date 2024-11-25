<template>
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-red-500 text-xl font-semibold">Error</h1>
            <p class="mt-2 text-gray-600">{{ error }}</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    error: String,
});
</script>
