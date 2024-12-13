<template>
    <table class="min-w-full table-auto">
        <thead class="bg-triviusBlue text-white border border-triviusBlue">
            <tr>
                <slot name="header"></slot>
            </tr>
        </thead>
        <tbody>
            <slot :rowClass="rowClass"></slot> <!-- Pass rowClass as a slot prop -->
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
    'border border-triviusBlue',
    props.hasHover || props.hasPointer
        ? 'cursor-pointer hover:bg-triviusBlueLight hover:text-triviusPink'
        : '',
].join(' '));
</script>
