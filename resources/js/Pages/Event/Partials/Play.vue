<script setup>
import { formatDate } from '@/utils';
import { router } from '@inertiajs/vue3';
import {ref, useTemplateRef, watch} from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';
import BubblesLayout from "@/Layouts/BubblesLayout.vue";
import DangerButton from "@/Components/DangerButton.vue";
import GameBubble from "@/Components/GameBubble.vue";
import BubblesContainer from "@/Components/BubblesContainer.vue";
import CountDown from "@/Components/CountDown.vue";

const props = defineProps({
    questions: Object,
    game:Object,
    round: {
        type: Number,
        default: 1
    }
});

const initialTime = 1200;
const page = ref("landing");

const timer = ref(initialTime);
let intervalId = null;
const isPaused = ref(false);
const questionNumber = ref(0);
const isRunning = ref(false);

const countdown = useTemplateRef('countdown');

const showBubbles = ref(true);
const showCountdown = ref(false);

const nextPrevActive = ref(false);


watch(questionNumber, (value, old) => {
    // console.log("QN Changed :: ", value, old);
    showBubbles.value = false;
    setTimeout(() => {
        showBubbles.value = true;
    }, 100);
});

watch(showBubbles, (value, old) => {
    // console.log("ShB Changed :: ", value, old);

    if(value) {
        setTimeout(() => {
            showCountdown.value = true;
        }, 1500);
        setTimeout(() => {
            nextPrevActive.value = true;
        }, 1800);
    } else {
        showCountdown.value = false;
    }
});


 if(props.round > 1) {
     questionNumber.value = 1;
}
// console.log("T", questionNumber.value, props.questions);

const endGame = () => {
    router.visit(route('games.endGame', { game: props.questions[0].game_id }));
};


const startCountdown = (onComplete) => {
  if (intervalId) {
    clearInterval(intervalId);
  }
  isPaused.value = false;
  isRunning.value = true;
  intervalId = setInterval(() => {
    if (!isPaused.value) {
      timer.value--;
      if (timer.value <= 0) {
        clearInterval(intervalId);
        intervalId = null;
        if (typeof onComplete === 'function') {
          onComplete();
        }
      }
    }
  }, 100);
};

const togglePauseResume = () => {
  if (intervalId) {
    isPaused.value = !isPaused.value;
    isRunning.value = !isRunning.value;
  }
};


const resetCountdown = () => {
// console.log("RESET INTERVAL", intervalId, countdown.value, countdown.value.status, countdown.value.resetTimer);

countdown.value.resetTimer();
  if (intervalId) {
    clearInterval(intervalId);
    isPaused.value = false;
    isRunning.value = false;
    intervalId = null;
  }
  timer.value = initialTime;
  isPaused.value = true;
};

const onComplete = () => {
  alert('Countdown completed!');
};

const newQuestion = (increment) => {
    if(!nextPrevActive.value) return;

    if((questionNumber.value+1) % 10 === 1){
        router.visit(route('games.endRound', { game: props.questions[0].game_id, round: props.round }));
    } else {
        //timer.value = 1200
        nextPrevActive.value = false;
        resetCountdown();
        questionNumber.value += increment
    }
}



</script>
<template>

    <BubblesLayout>
        <template #header>
            <div class="flex content-center">
                <div class="font-bold text-xl text-center text-white mx-auto justify-center" v-if="game.status !== 'bonus'">Round {{ round }}</div>
                <div class="font-bold text-xl text-center text-white mx-auto justify-center" v-else>Bonus Round</div>
            </div>
        </template>

        <template #bubbles>
            <BubblesContainer v-if="showBubbles">
                <template v-if="questionNumber === 0">
                    <GameBubble>
                        <div v-if="game.status !== 'bonus'">
                            Landing Page Text
                        </div>
                        <div v-else>
                            Bonus Round Text
                        </div>
                    </GameBubble>
                </template>
                <template v-else>
                    <GameBubble slide-in="left">
                        <div class="text-triviya-red text-sm font-bold">Question {{ (questions[questionNumber-1].question_number % 10 === 0) ? 10 : questions[questionNumber-1].question_number % 10  }} of 10</div>
                        {{questions[questionNumber-1].question_text}}
                    </GameBubble>
                    <GameBubble color="white" subtract="left" slide-in="right" slide-in-delay="1000">
                        <div>
                            <div>
                                <div><span class="font-bold text-triviya-red text-sm">Who Said...</span></div>
                                <div>{{questions[questionNumber-1].answer}}</div>
                            </div>
                        </div>

                    </GameBubble>
                    <transition name="fade">
                        <CountDown v-if="showCountdown" ref="countdown"></CountDown>
                    </transition>
                </template>
            </BubblesContainer>
        </template>



        <template #question-buttons>
            <div v-if="questionNumber === 0">
                <PrimaryButton @click="questionNumber = 1" class="my-4">
                    &nbsp;Go to first<span v-if="game.status === 'bonus'">&nbsp;Bonus&nbsp;</span> question
                </PrimaryButton>
            </div>
            <div v-else>
                <PrimaryButton :disabled="!nextPrevActive" @click="newQuestion(-1)" class="my-2 mr-2 " :class="['my-2 mr-2', { 'opacity-50 cursor-not-allowed': questionNumber === 1 }]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 h-2 rotate-180">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                    Previous
                </PrimaryButton>
                <PrimaryButton :disabled="!nextPrevActive" @click="newQuestion(+1)" class="my-2">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 h-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                </PrimaryButton>
            </div>
        </template>

    </BubblesLayout>

</template>

<style scoped>
.fade-enter-from {
    opacity: 0;
}

.fade-enter-to {
    opacity: 1;
}

.fade-enter-active {
    transition: opacity 2.0s linear;
}

</style>
