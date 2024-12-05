<template>
    <MaskInput
      v-bind="$attrs"
      v-on="eventListeners"
      :class="['border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-800', customClass]"
    />
  </template>

  <script setup>
  import { MaskInput } from 'vue-mask-next'; // Import the original MaskInput component

  // Define props only once
  const props = defineProps({
    customClass: {
      type: String,
      default: '',
    },
  });

  // Access all attributes passed to the component
  const eventListeners = Object.keys(props).reduce((listeners, key) => {
    if (key.startsWith('on')) {  // Check if the key is an event handler (e.g., onUpdate:modelValue)
      const event = key.slice(2).toLowerCase(); // Convert 'onUpdate:modelValue' to 'update:modelValue'
      listeners[event] = props[key];
    }
    return listeners;
  }, {});
  </script>
