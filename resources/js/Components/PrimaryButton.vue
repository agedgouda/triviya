<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-triviusPurple-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-triviusPurple-700 focus:bg-triviusPurple-700 active:bg-triviusPurple-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>

