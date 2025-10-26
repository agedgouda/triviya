<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
    target: {
        type: String,
        default: null,
    },
});

const classes = computed(() => {
    return props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-triviya-red text-start text-base font-medium text-white focus:outline-none focus:text-indigo-800 focus:bg-triviya-lightRed focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-triviya-lightRed  hover:text-triviya-red hover:bg-triviya-lightRed hover:border-triviya-red focus:outline-none focus:text-triviya-red focus:bg-triviya-red focus:border-triviya-red transition duration-150 ease-in-out';
});

// detect internal vs external
const isInternal = computed(() => !/^https?:\/\//.test(props.href));
</script>

<template>
    <div>
        <button v-if="as === 'button'" :class="classes" class="w-full text-start">
            <slot />
        </button>

        <component
            v-else
            :is="isInternal ? Link : 'a'"
            :href="href"
            :class="classes"
            v-bind="!isInternal && props.target ? { target: props.target, rel: 'noopener noreferrer' } : {}"
        >
            <slot />
        </component>
    </div>
</template>
