<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-triviusBlue border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-triviusBlueLight focus:bg-triviusBlue active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-triviusPink focus:ring-offset-2 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
