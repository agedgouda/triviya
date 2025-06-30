<script setup>
defineProps({
    value: String,
    sizeClass: {
        type: String,
        default: 'text-lg font-bold'
    }
});
</script>

<template>
    <label class="block" :class="[sizeClass]">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
