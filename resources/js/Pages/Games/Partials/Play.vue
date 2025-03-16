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
});

const page = ref("landing")
const questionNumber = ref(0)
const timer = ref(30)
let intervalId = null


const startCountdown = (onComplete) => {
  // Clear any previous interval to avoid overlapping timers
  if (intervalId) {
    clearInterval(intervalId)
  }
  // Reset the timer to the starting value
  timer.value = 300

  intervalId = setInterval(() => {
    timer.value--
    if (timer.value <= 0) {
      clearInterval(intervalId)
      intervalId = null
      // When countdown completes, call the onComplete callback if provided
      if (typeof onComplete === 'function') {
        onComplete()
      }
    }
  }, 100)
}

//startCountdown()
const newQuestion = () => {
    page.value = "question";
    questionNumber.value += 1
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
    <div v-else>
        <div>Round {{ Math.floor(questionNumber/10)+1  }}</div>
        <div>Question {{ questions[questionNumber-1].question_number%10  }}</div>
        Who answered "<span>{{questions[questionNumber-1].question_text}}</span>""
        <div>{{questions[questionNumber-1].answer}}</div>
        <PrimaryButton @click="questionNumber -= 1" class="my-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 h-2 rotate-180">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
            </svg>
            Previous
        </PrimaryButton>
        <PrimaryButton @click="questionNumber += 1" class="ml-2 my-2">
            Next
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 h-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
            </svg>
        </PrimaryButton>

        {{  timer/10 }}

    </div>
</template>

