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
    console.log('End Game')
    router.visit(route('games.endGame', { game: props.answers[0].game_id}));
}

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
    <div v-for="answer in answers">
        {{ answer.question_number }}.  {{ answer.question_text }}<br>
        {{ answer.answer }}<br>
        <strong>{{ answer.player_name }}</strong>
        <hr class="h-px my-4 bg-gray-500 border-0">
    </div>
    <PrimaryButton @click="newRound(round+1)" v-if="round <= 2">
        &nbsp;Go to Round {{ round+1 }}
    </PrimaryButton>
    <DangerButton @click="endGame" v-else>
        &nbsp;End Game
    </DangerButton>
</template>

