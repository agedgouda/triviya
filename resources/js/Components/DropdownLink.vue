<script setup>
import { computed, useAttrs } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const attrs = useAttrs();

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-triviya-red text-sm font-medium leading-5 text-triviya-red focus:outline-none focus:border-triviya-lightRed transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-triviya-red hover:text-triviya-red hover:border-triviya-red focus:outline-none focus:text-triviya-lightRed focus:border-gray-300 transition duration-150 ease-in-out';
});
</script>

<template>
    <div>
        <button
            v-if="as === 'button'"
            :class="classes"
            v-bind="attrs"
            class="w-full text-start"
        >
            <slot />
        </button>

        <a
            v-else-if="as === 'a'"
            :href="href"
            :class="classes"
            v-bind="attrs"
            class="w-full text-start"
        >
            <slot />
        </a>

        <Link
            v-else
            :href="href"
            :class="classes"
            v-bind="attrs"
        >
            <slot />
        </Link>
    </div>
</template>
