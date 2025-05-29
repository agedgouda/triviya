<script setup>
defineProps({
    value: String,
    sizeClass: {
        type: String,
        default: 'text-3xl'
    }
});
</script>

<template>
    <label class="block font-medium" :class="[sizeClass]">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
