<script setup>
import { flashMessage } from '@/Composables/useFlash';
</script>

<template>
  <transition
    enter-active-class="transition-all duration-500 ease-out"
    enter-from-class="opacity-0 -translate-y-12"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition-all duration-500 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-12"
  >
    <div v-if="flashMessage" class="fixed top-0 left-1/2 transform -translate-x-1/2 bg-triviya-red text-white px-4 py-2 rounded-b shadow-lg z-50">
      {{ flashMessage }}
    </div>
  </transition>
</template>
