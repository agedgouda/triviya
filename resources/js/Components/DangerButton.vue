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
      class="
        inline-flex items-center px-4 py-2
        bg-triviya-black border border-transparent
        rounded-md font-semibold text-xs uppercase tracking-widest
        text-white
        hover:bg-triviya-darkGray hover:text-white
        focus:bg-triviya-darkGray focus:text-white
        active:bg-white active:text-black
        focus:outline-none focus:ring-2 focus:ring-triviya-red focus:ring-offset-2
        transition ease-in-out duration-150
      "
    >
      <slot />
    </button>
  </template>

