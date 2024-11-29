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
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-amber-500 text-start text-base font-medium text-amber-400  focus:outline-none focus:text-indigo-800 focus:bg-amber-700 focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-amber-400 hover:text-amber-400 hover:bg-amber-700 hover:border-amber-300 focus:outline-none focus:text-amber-300 focus:bg-amber-300 focus:border-amber-300 transition duration-150 ease-in-out';
});
</script>
? 'inline-flex items-center px-1 pt-1 border-b-2 border-amber-500 text-sm font-medium leading-5 text-amber- focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-amber-400 hover:text-amber-700 hover:border-amber-300 focus:outline-none focus:text-amber-700 focus:border-gray-300 transition duration-150 ease-in-out';

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
