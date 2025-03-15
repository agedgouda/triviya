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

const page = ref("interstitial")
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

const newQuestion = () => {
    page.value = "question";
    questionNumber.value += 1
    startCountdown(() => {
        page.value = "interstitial";
    })
}

</script>
<template>

<div v-for="(question, index) in questions">
   {{ index+1 }}. {{ question.player_name }}:  {{ question.question_text }}
</div>
<div>Round {{ Math.floor(questionNumber/10)+1  }}</div>
    <div v-if="page  === 'interstitial'">
        <div v-if="questionNumber  === 0">
            Landing Page Text
        </div>
        <div v-else>
            Text before {{ questionNumber+1  }}
        </div>
        <PrimaryButton @click="newQuestion()" class="my-4">
            &nbsp;Go to question {{ questionNumber+1  }}
        </PrimaryButton>
    </div>
    <div v-if="page  === 'question'">
        Who answered "<span>{{questions[questionNumber].question_text}}</span>""
        <div>{{questions[questionNumber].answer}}</div>

        {{  timer/10 }}

    </div>
</template>

