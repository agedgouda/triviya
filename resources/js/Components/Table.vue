<template>
    <table class="min-w-full table-auto">
        <thead class="bg-triviyaRegular text-white border border-triviyaRegular">
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
    'border-b-8 3 border-triviya-lightBackground bg-white',
    props.hasHover || props.hasPointer
        ? 'cursor-pointer hover:bg-triviya-redHover hover:text-white'
        : '',
].join(' '));
</script>
