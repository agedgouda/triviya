<template>
  <table class="min-w-full max-w-full table-fixed border-collapse text-xs sm:text-sm md:text-base break-words">
    <thead class="bg-triviyaRegular text-white border border-triviyaRegular">
      <tr>
        <!-- Slot for headers. Recommended to set widths per column -->
        <slot name="header"></slot>
      </tr>
    </thead>
    <tbody>
      <!-- Slot for rows. rowClass prop passed -->
      <slot :rowClass="rowClass"></slot>
    </tbody>
  </table>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    hasHover: {
        type: Boolean,
        default: false,
    },
    hasPointer: {
        type: Boolean,
        default: false,
    },
});

const rowClass = computed(() => [
  'border-b-4 sm:border-b-8 border-triviya-lightBackground bg-white', // thinner border on mobile
  'px-2 sm:px-4 py-1 sm:py-2', // add padding to table rows if not set on td
  (props.hasHover || props.hasPointer) ? 'cursor-pointer hover:bg-triviya-redHover hover:text-white' : ''
].join(' '));

</script>
