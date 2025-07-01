<script setup>
const props = defineProps({
  /**
   * The button's HTML `type`, e.g. "submit" or "button". */
  type: {
    type: String,
    default: 'submit',
  },
  /**
   * Disable the button (grayed out + not clickable). */
  disabled: {
    type: Boolean,
    default: false,
  },
  /**
   * Use `processing` to indicate action in progress (also disables). */
  processing: {
    type: Boolean,
    default: false,
  },
});
</script>

<template>
  <button
    :type="props.type"
    :disabled="props.disabled || props.processing"
    v-bind="$attrs"
    :class="[
    'inline-flex items-center px-4 py-2 bg-triviya-red border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-triviya-red focus:ring-offset-2 transition ease-in-out duration-150',
    (props.disabled || props.processing)
        ? 'opacity-25 cursor-not-allowed pointer-events-none'
        : 'hover:bg-triviya-redHover focus:bg-triviya-redHover active:bg-triviya-redPress'
    ]"
  >
    <slot />
  </button>
</template>
