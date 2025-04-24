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
      class="inline-flex items-center px-4 py-2 bg-triviya-red border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-triviya-redHover focus:bg-triviya-redHover active:bg-triviya-redPress focus:outline-none focus:ring-2 focus:ring-triviya-red focus:ring-offset-2 transition ease-in-out duration-150"
    >
      <slot />
    </button>
  </template>


