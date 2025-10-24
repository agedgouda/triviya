<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: String,
  active: Boolean,
  hasSubnav: {
    type: Boolean,
    default: false,
  },
  target: {
    type: String,
    default: null,
  },
})

const isHovered = ref(false)

const classes = computed(() => {
  return props.active
    ? 'inline-flex items-center px-1 pt-1 text-sm font-bold leading-5 text-white focus:outline-none transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-triviya-lightRed text-sm font-bold leading-5 text-blue-50 hover:text-triviya-red hover:border-triviya-red focus:outline-none transition duration-150 ease-in-out'
})

// check if href is an internal route (no http:// or https://)
const isInternal = computed(() => !/^https?:\/\//.test(props.href))
</script>

<template>
  <div
    @mouseover="isHovered = true"
    @mouseleave="isHovered = false"
    :class="classes"
    class="relative"
  >
    <!-- Main Navigation Link -->
    <component
      :is="isInternal ? Link : 'a'"
      :href="href"
      v-bind="!isInternal && props.target ? { target: props.target, rel: 'noopener noreferrer' } : {}"
    >
      <slot />
    </component>

    <!-- Subnav Dropdown -->
    <div
      v-if="isHovered && hasSubnav"
      class="absolute w-48 bg-teal-700 pl-1 pt-1 flex flex-col z-50 border-t border-triviyaLight"
      style="transform: translateY(75%) translateX(-8%);"
    >
      <slot name="subnav" />
    </div>
  </div>
</template>
