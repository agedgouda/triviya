<script setup>
import { ref, computed } from 'vue'
import TextInput from './TextInput.vue'

const props = defineProps({
  id: String,
  name: String,
  modelValue: String,
  placeholder: String,
  required: Boolean,
  autocomplete: String,
  inputClass: {
    type: String,
    default: 'block w-full pr-10'
  }
})

const emit = defineEmits(['update:modelValue'])
const show = ref(false)

const model = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value)
})
</script>

<template>
  <div class="relative">
    <TextInput
      :id="id"
      :name="name"
      :type="show ? 'text' : 'password'"
      :placeholder="placeholder"
      :class="inputClass"
      v-model="model"
      :required="required"
      :autocomplete="autocomplete"
    />
    <button
      type="button"
      @click="show = !show"
      class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
      aria-label="Toggle password visibility"
    >
      <svg v-if="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7
                 -1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
      </svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943
                 -9.542-7a9.966 9.966 0 012.563-4.303M9.88 9.88a3 3 0 104.24 4.24" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3l18 18" />
      </svg>
    </button>
  </div>
</template>
