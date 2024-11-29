<template>
    <table class="min-w-full table-auto">
        <thead class="bg-amber-500 text-amber-700 border border-amber-500">
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
    'border border-amber-400',
    props.hasHover || props.hasPointer
        ? 'cursor-pointer hover:bg-amber-700 hover:text-amber-400'
        : '',
].join(' '));
</script>
