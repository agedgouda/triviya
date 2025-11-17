<script setup>
import {nextTick, ref} from "vue";

const defaultDuration = 20;
// const defaultDuration = 8;

const status = ref('idle');
const secondsLeft = ref(defaultDuration);
const spinnerProgress = ref(false);

const beep = new Audio('https://soundbible.com/mp3/Elevator Ding-SoundBible.com-685385892.mp3');
beep.load();

let theTimeout = null;

const startTimer = () => {
    if(secondsLeft.value >= 1) {
        spinnerProgress.value = true
        theTimeout = setTimeout(() => {
            secondsLeft.value -= 1;

            if(secondsLeft.value <= 0) {
                beep.pause();
                beep.currentTime = 0;
                beep.play();
            }

            // spinnerProgress.value = false;
            startTimer();
        }, 1000);

    } else {
        spinnerProgress.value = false
        status.value = 'finish';
        clearTimeout(theTimeout);
    }
}

const switchToPlay = () => {
    if(status.value !== 'idle') return;

    status.value = 'switching';

    setTimeout(() => {
        status.value = 'progress';

        setTimeout(() => {
            startTimer();
        }, 50);

    }, 500);
}

const resetTimer = () => {
    clearTimeout(theTimeout);
    secondsLeft.value = defaultDuration;
    status.value = 'idle';
}

defineExpose({resetTimer, status});


</script>

<template>
    <div class="flex justify-center">
        <div v-if="status === 'idle' || status=== 'switching'" class="mb-8 idle-container" @click="switchToPlay()" :class="{switching: status === 'switching'}">
            <div class="hour-glass-outer flex justify-center">
                <div class="hour-glass-inner flex justify-center align-center">
                    <div class="hour-glass m-auto" v-if="status === 'idle'"></div>
                    <div class="m-auto count-proxy text-white" v-text="defaultDuration" v-if="status === 'switching'"></div>
                </div>
            </div>
            <transition name="fade">
                <div class="mt-4 text-white tap-to" v-if="status === 'idle'">
                    TAP TO START<br />COUNTDOWN
                </div>
            </transition>
        </div>

        <div class="mb-8 counter-container" v-if="status === 'progress'">
            <div class="counter-outer flex justify-center relative">
                <div class="absolute counter-spinner" :class="{spinning: spinnerProgress}"></div>
                <div class="counter-inner flex justify-center align-center">
                    <div class="m-auto count-value text-white" v-text="secondsLeft"></div>
                </div>
            </div>
            <div class="mt-4 text-white text-center"><a @click.prevent="resetTimer()" href="#">RESET</a></div>
        </div>

        <div class="mb-8 times-up-container" v-if="status === 'finish'">
            <div class="times-up-outer flex justify-center relative">
                <div class="times-up-inner flex justify-center align-center bg-white">
                    <div class="m-auto times-up-text text-center text-triviya-darkPurple">TIMES<br />UP!</div>
                </div>
            </div>
            <div class="mt-4 text-white text-center"><a @click.prevent="resetTimer()" href="#">RESET</a></div>
        </div>

    </div>
</template>

<style scoped>
.hour-glass-inner {
    background-image: url("~/images/small-eclipse.png");
    background-size: contain;
    height: 50px;
    aspect-ratio: 1;
}

.counter-inner {
    /*background-image: url("/images/small-eclipse.png");*/
    background-size: contain;
    height: 60px;
    aspect-ratio: 1;
}

.count-value {
    font-size: 2rem;
    z-index: 5;
}

.hour-glass {
    background-image: url("~/images/hour-glass.png");
    height:30px;
    width: 13.5px;
    background-size: contain;
    background-repeat: no-repeat;
}

.idle-container {
    cursor: pointer;
}

.idle-container.switching .hour-glass-inner{
    height: 60px;
    transition: all 0.3s linear;
    background-image: url("~/images/spinner-base.png");
}

@keyframes hourglass {
    0%   {rotate: 0deg}
    100% {rotate:360deg}
}

.count-proxy {
    font-size: 1.5rem;
}

.idle-container.switching .count-proxy{
    font-size: 2rem;
    transition: all 0.3s linear;

}

.counter-spinner {
    left:0;
    top: 0;
    width: 60px;
    height: 60px;
    background-image: url("~/images/spinner-base.png");
    background-size: contain;
    rotate: 0deg;
}

.counter-spinner.spinning {
    /*rotate: 360deg;
    transition: all 1s linear; */
    animation-name: hourglass;
    animation-iteration-count: infinite;
    animation-duration: 1.0s;
    animation-timing-function: linear;
}


.times-up-inner {
    height: 60px;
    aspect-ratio: 1;
    border-radius: 50%;
}

.tap-to {
    line-height: 1rem;
}

.times-up-text {
    line-height: 1rem;
    animation-name: bounce-in;
    animation-duration: 0.5s;
    animation-iteration-count: 1;
}

@keyframes bounce-in {
    0% {
        opacity: 0;
        transform: scale(.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.1);
    }
    70% { transform: scale(.9); }
    100% { transform: scale(1); }
}

</style>
