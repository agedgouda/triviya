<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'bg-amber-300'],
    },
    active: Boolean,
});

let open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        '48': 'w-48',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0';
    }

    if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0';
    }

    return 'origin-top';
});

const classes = computed(() => {
    return props.active
        ? ' inline-flex items-center px-1 pt-1 border-b-2 border-amber-500 text-sm font-medium leading-5 text-amber-400 focus:outline-none focus:border-amber-700 transition duration-150 ease-in-out'
        : ' inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-amber-500 hover:text-amber-700 hover:border-amber-300 focus:outline-none focus:text-amber-700 focus:border-gray-300 transition duration-150 ease-in-out';
});


</script>

<template>
    <div :class="classes">
        <!-- Trigger Styled Exactly Like NavLink -->
        <div @mouseover="open = ! open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="fixed inset-0 z-40" @click="open = false" />

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <!-- Dropdown Menu -->
            <div
                v-show="open"

                :class="[widthClass, alignmentClasses]"
                style="display: none;"
                @click="open = false"
            >
                <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>
