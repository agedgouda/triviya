<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
      :type="type"
      class="inline-flex items-center px-4 py-2 bg-transparent border border-triviya-red rounded-md font-semibold text-xs text-triviya-red uppercase tracking-widest hover:bg-triviya-redHover hover:text-white focus:bg-triviya-redHover focus:text-white active:bg-triviya-redPress active:text-white focus:outline-none focus:ring-2 focus:ring-triviya-red focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
    >
      <slot />
    </button>
</template>
