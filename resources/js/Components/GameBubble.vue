<script setup>
import {onMounted, ref} from "vue";

const props = defineProps({
    hasBackground: {
        type:Boolean,
        default: true,
    },
    color: {
        type:String,
        default: "purple"
    },
    subtract: {
        type:String,
        default: null
    },

    slideIn: {
        type:String,
        default: null,
    },

    slideInDelay: {
        type: Number,
        default: 200,
    }
    // game: Object,
    // user: Object,
});

let showMe = ref(false);

onMounted(() => {

    if(!props.slideIn) {
        showMe.value = true;
    } else {
        setTimeout(() => {
            showMe.value = true;
        }, props.slideInDelay)
    }

    // console.log(showMe);
    // console.log(props);
})


</script>

<template>
<div class="bubble-root mb-4">
    <transition :name="`slide-${slideIn}`">
        <div v-show="showMe" class="relative">
            <div v-if="color==='purple'" :class="[
                                hasBackground ? 'bg-triviya-darkPurple shadow-md overflow-hidden sm:rounded-lg' : '',
                                'p-6 mb-6 text-white mx-auto max-w-xl rounded-xl relative'
                            ]"
            ><slot /></div>
            <div v-else-if="color==='white'">
                <div class="bg-white shadow-md sm:rounded-lg p-6 mb-6 mx-auto max-w-xl rounded-xl relative">
                    <slot />
                    <div v-if="subtract === 'left'" style="position: absolute; bottom:-20px;left:10px;width: 26px;height:20px;background-image: url(/images/subtract.png);background-repeat: no-repeat;background-size: contain;"></div>
                    <div v-if="subtract === 'right'" style="position: absolute; bottom:-20px;right:10px;transform:scaleX(-1); width: 26px;height:20px;background-image: url(/images/subtract.png);background-repeat: no-repeat;background-size: contain;"></div>
                </div>
            </div>
        </div>
    </transition>
</div>
</template>

<style scoped>
.slide-left-enter-from {
    opacity: 0;
    left: -300px;
}

.slide-left-enter-to {
    opacity: 1;
    left: 0;
}

.slide-left-enter-active {
    transition: opacity 2.0s linear, left 1.0s cubic-bezier(0.3, 0.2, 0.2, 1.4);
}

.slide-right-enter-from {
    opacity: 0;
    right: -300px;
}

.slide-right-enter-to {
    opacity: 1;
    right: 0;
}

.slide-right-enter-active {
    transition: opacity 2.0s ease, right 1.0s cubic-bezier(0.3, 0.2, 0.2, 1.4);
}


</style>
