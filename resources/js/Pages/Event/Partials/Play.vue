<script setup>
import { formatDate } from '@/utils';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Table from '@/Components/Table.vue';

const props = defineProps({
    questions: Object,
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

 if(props.round > 1) {
     questionNumber.value = 1;
}


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
    if((questionNumber.value+1) % 10 === 1){
        router.visit(route('games.endRound', { game: props.questions[0].game_id, round: props.round }));
    } else {
        timer.value = 1200
        questionNumber.value += increment
    }
}

</script>
<template>

    <div v-if="questionNumber  === 0">
        <div >
            Landing Page Text
        </div>
        <PrimaryButton @click="questionNumber = 1" class="my-4">
            &nbsp;Go to first question
        </PrimaryButton>
    </div>

    <div v-if="questionNumber  > 0 && questionNumber  <= 10">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <div>Round {{ round }}</div>
                <div>Question {{ (questions[questionNumber-1].question_number % 10 === 0) ? 10 : questions[questionNumber-1].question_number % 10  }}</div>
                <div><span class="font-bold">For the question</span> {{questions[questionNumber-1].question_text}}</div>
                <div><span class="font-bold">Who answered</span> {{questions[questionNumber-1].answer}}<span class="font-bold">?</span></div>
                <PrimaryButton @click="newQuestion(-1)" class="my-2 mr-2 " :class="['my-2 mr-2', { 'opacity-50 cursor-not-allowed': questionNumber === 1 }]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 h-2 rotate-180">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                    Previous
                </PrimaryButton>
                <PrimaryButton @click="newQuestion(+1)" class="my-2">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 h-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                </PrimaryButton>
            </div>
            <div>
                <h2>Timer</h2>
                <div>{{  timer/10 }} seconds</div>

                <PrimaryButton @click="startCountdown()" class="my-2" v-if="!isRunning">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                </PrimaryButton>
                <PrimaryButton @click="togglePauseResume()" class="my-2" v-if="isRunning">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                    </svg>
                </PrimaryButton>
                <PrimaryButton @click="resetCountdown()" class="my-2 ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                    </svg>
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>

