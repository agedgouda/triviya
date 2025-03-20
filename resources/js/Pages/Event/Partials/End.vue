<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    answers: Object,
    round: Number,
});
const newRound = (round) => {
    router.visit(route('games.startRound', { game: props.answers[0].game_id, round: round }));
}
const endGame = () => {
    router.visit(route('games.endGame', { game: props.answers[0].game_id}));
}
const questionNumber = ref(0);

</script>
<template>
    <div class="flex">
        <div class="font-bold text-xl">Round {{ round }} Answers</div>
            <div class="ml-auto">
                <PrimaryButton @click="newRound(round+1)" v-if="round <= 2">
                    &nbsp;Go to Round {{ round+1 }}
                </PrimaryButton>
                <DangerButton @click="endGame" v-else>
                    &nbsp;End Game
                </DangerButton>
            </div>
        </div>
        <div v-if="questionNumber === 0">
            Begin Showing Answers
        </div>
        <div v-if="questionNumber > 0 && questionNumber <=10">
                <div>Round {{ round }}</div>
                <div>Question {{ (answers[questionNumber-1].question_number % 10 === 0) ? 10 : answers[questionNumber-1].question_number % 10  }}</div>
            <div><span class="font-bold">For the question</span> {{ answers[questionNumber-1].question_text }}</div>
            <div><span class="font-bold">Who answered</span> {{ answers[questionNumber-1].answer }}<span class="font-bold">?</span></div>
            <div class="font-bold text-red-500">{{ answers[questionNumber-1].player_name }}</div>
        </div>
        <div v-else>
            Next Round
        </div>
     <div v-if="questionNumber  === 0">
        <div >
            Landing Page Text
        </div>
        <PrimaryButton @click="questionNumber = 1" class="my-4">
            &nbsp;Go to first question
        </PrimaryButton>
    </div>
    <div v-if="questionNumber  > 0 && questionNumber <10">
        <PrimaryButton @click="questionNumber -= 1" :class="['my-2 mr-2', { 'opacity-50 cursor-not-allowed': questionNumber === 1 }]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 h-2 rotate-180">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
            </svg>
            Previous
        </PrimaryButton>
        <PrimaryButton @click="questionNumber += 1"  class="my-2">
            Next
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 h-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
            </svg>
        </PrimaryButton>
    </div>
    <div v-if="questionNumber >= 10" class="my-4">
        <PrimaryButton @click="newRound(round+1)" v-if="round <= 2">
            &nbsp;Go to Round {{ round+1 }}
        </PrimaryButton>
        <DangerButton @click="endGame" v-else>
            &nbsp;End Game
        </DangerButton>
    </div>
</template>

