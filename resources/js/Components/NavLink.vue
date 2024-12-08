<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    hasSubnav: {
        type:Boolean,
        default: false
    }
});

const isHovered = ref(false);

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-amber-500 text-sm font-medium leading-5 text-amber-400 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-amber-500 hover:text-amber-700 hover:border-amber-300 focus:outline-none focus:text-amber-700 focus:border-gray-300 transition duration-150 ease-in-out';
});
</script>

<template>
    <div
        @mouseover="isHovered = true"
        @mouseleave="isHovered = false"
        :class="classes"
        class="relative"
    >
        <!-- Main Navigation Link -->
        <Link :href="href">
            <slot />
        </Link>

        <!-- Subnav Dropdown -->
        <div
            v-if="isHovered && hasSubnav"
            class="absolute w-48 bg-teal-700 pl-1 pt-1 flex flex-col z-50 border-t border-amber-400 "
            style="transform: translateY(75%) translateX(-8%);"
        >
            <slot name="subnav" />
        </div>
    </div>
</template>
