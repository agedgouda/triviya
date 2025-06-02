<script setup>
import { router } from '@inertiajs/vue3';
import {ref, watch} from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import BubblesLayout from "@/Layouts/BubblesLayout.vue";
import BubblesContainer from "@/Components/BubblesContainer.vue";
import GameBubble from "@/Components/GameBubble.vue";

const props = defineProps({
    answers: Object,
    round: Number,
    game: Object,
});


const newRound = (round) => {
    router.visit(route('games.startRound', { game: props.answers[0].game_id, round: round }));
}
const endGame = () => {
    router.visit(route('games.endGame', { game: props.answers[0].game_id}));
}
const questionNumber = ref(0);
const showBubbles = ref(true);

const buttonsEnabled = ref(false);

watch(questionNumber, (value, old) => {
    // console.log("QN Changed :: ", value, old);
    showBubbles.value = false;
    buttonsEnabled.value = false;
    setTimeout(() => {
        showBubbles.value = true;
    }, 100);

    setTimeout(() => {
        buttonsEnabled.value = true;
    }, 1500);
});

</script>
<template>
    <BubblesLayout>
        <template #header>
            <div class="flex content-center">
                <div class="font-bold text-xl text-center text-white mx-auto justify-center" v-if="game.status !== 'bonus'">Round {{ round }} Answers</div>
                <div class="font-bold text-xl text-center text-white mx-auto justify-center" v-else>Bonus Round Answers</div>
                <div class="ml-auto absolute right-0" >
<!--                    {{(round <= 2 && game.status==='ready')}}-->
                    <PrimaryButton @click="newRound(round+1)" v-if="round <= 2 && game.status==='ready' ">
                        &nbsp;Go to Round {{ round+1 }}
                    </PrimaryButton>
<!--                    <DangerButton @click="endGame" v-else>-->
<!--                        &nbsp;End Game-->
<!--                    </DangerButton>-->
                </div>
            </div>
        </template>

        <template #bubbles>
            <BubblesContainer v-if="showBubbles">
                <GameBubble v-if="questionNumber === 0" :has-background="true">
                    Begin Showing Answers
                </GameBubble>
                <template v-if="questionNumber > 0 && questionNumber <=10">
                <GameBubble  :has-background="true" slide-in="left">
<!--                    <div v-if="game.status !== 'bonus'">Round {{ round }}</div>-->
<!--                    <div v-else>Bonus Round</div>-->
                    <div class="text-triviya-red text-sm font-bold">Question {{ (answers[questionNumber-1].question_number % 10 === 0) ? 10 : answers[questionNumber-1].question_number % 10  }} of {{answers.length}}</div>
                    <div><span class="font-bold">{{ answers[questionNumber-1].question_text }}</span></div>
                </GameBubble>
                <GameBubble :has-background="true" slide-in="right">
                    <div><span class="font-bold text-triviya-red text-sm">Who Said ...</span></div>
                    <div>
                        {{ answers[questionNumber-1].answer }}
                    </div>
                </GameBubble>
                <GameBubble :has-background="true" color="white" subtract="right" slide-in="left" :slide-in-delay="1000">
                    <div><span class="font-bold text-triviya-red text-sm">Answer</span></div>
                    <div class="font-bold text-black text-lg">{{ answers[questionNumber-1].player_name }}</div>
                </GameBubble>
                </template>
                <div v-else>
                    <GameBubble v-if="game.status !== 'bonus'" :has-background="true" color="white">
                        Next Round
                    </GameBubble>
                    <GameBubble v-else :has-background="false" color="white">
                        Bonus Round
                    </GameBubble>
                </div>
            </BubblesContainer>
        </template>

        <template #question-buttons>
            <div v-if="questionNumber  === 0">
                <PrimaryButton @click="questionNumber = 1" class="my-4">
                    &nbsp;Go to first question
                </PrimaryButton>
            </div>
            <div v-if="questionNumber > 0 && questionNumber <10">
                <PrimaryButton :disabled="!buttonsEnabled" @click="questionNumber -= 1" :class="['my-2 mr-2', { 'opacity-50 cursor-not-allowed': questionNumber === 1 }]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 h-2 rotate-180">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                    Previous
                </PrimaryButton>
                <PrimaryButton :disabled="!buttonsEnabled" @click="questionNumber += 1"  class="my-2">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 h-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                </PrimaryButton>
            </div>
            <div v-if="questionNumber >= 10" class="my-4">
                <PrimaryButton :disabled="!buttonsEnabled" @click="newRound(round+1)" v-if="round <= 2 && game.status !== 'bonus'">
                    &nbsp;Go to Round {{ round+1 }}
                </PrimaryButton>
                <DangerButton :disabled="!buttonsEnabled" @click="endGame" v-else>
                    &nbsp;End Game
                </DangerButton>
            </div>

        </template>
    </BubblesLayout>
</template>

