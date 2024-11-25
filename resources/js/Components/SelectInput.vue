<script setup>
import { computed, toRefs } from 'vue';

// Emit an event when the value changes
const emit = defineEmits(['update:selected']);

// Define props for the component
const props = defineProps({
  options: {
    type: Array,
    required: true, // Array of options for the select element
  },
  selected: {
    type: [String, Number], // The selected value
    default: null,
  },
  placeholder: {
    type: String,
    default: 'Select an option', // Default placeholder text
  },
});

// Create a computed property to proxy the selected value
const proxySelected = computed({
  get() {
    return props.selected;
  },
  set(val) {
    emit('update:selected', val);
  },
});
</script>

<template>
  <select
    v-model="proxySelected"
    class="rounded border-gray-300 text-gray-800 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
  >
    <!-- Optional placeholder -->
    <option value="" disabled>{{ props.placeholder }}</option>

    <!-- Render the options dynamically from the 'options' prop -->
    <option
      v-for="(option, index) in props.options"
      :key="index"
      :value="option.value"
    >
      {{ option.label }}
    </option>
  </select>
</template>
