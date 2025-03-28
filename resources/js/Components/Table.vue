<template>
    <table class="min-w-full table-auto">
        <thead class="bg-triviusRegular text-white border border-triviusRegular">
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
    'border border-triviusRegular',
    props.hasHover || props.hasPointer
        ? 'cursor-pointer hover:bg-triviusLight hover:text-red-600'
        : '',
].join(' '));
</script>
