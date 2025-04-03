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
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-900 text-sm font-bold leading-5 text-indigo-900  focus:outline-none focus:border-triviyaRegular transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-blue-50 hover:text-triviyaLight hover:border-triviyaLight focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
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
            class="absolute w-48 bg-teal-700 pl-1 pt-1 flex flex-col z-50 border-t border-triviyaLight "
            style="transform: translateY(75%) translateX(-8%);"
        >
            <slot name="subnav" />
        </div>
    </div>
</template>
