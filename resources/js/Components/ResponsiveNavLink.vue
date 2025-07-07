<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});


const classes = computed(() => {
    return props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-triviya-red text-start text-base font-medium text-white focus:outline-none focus:text-indigo-800 focus:bg-triviya-lightRed focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-triviya-lightRed  hover:text-triviya-red hover:bg-triviya-lightRed hover:border-triviya-red focus:outline-none focus:text-triviya-red focus:bg-triviya-red focus:border-triviya-red transition duration-150 ease-in-out';
});
</script>
? 'inline-flex items-center px-1 pt-1 border-b-2 border-triviya-red text-sm font-medium leading-5 text-triviya-red focus:outline-none focus:border-triviya-lightRed transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-triviya-red hover:text-triviya-red hover:border-triviya-red focus:outline-none focus:text-triviya-lightRed focus:border-gray-300 transition duration-150 ease-in-out';

<template>
    <div>
        <button v-if="as == 'button'" :class="classes" class="w-full text-start">
            <slot />
        </button>

        <a v-else-if="as == 'a'" :class="classes" class="w-full text-start" :href="href">
            <slot />
        </a>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>
