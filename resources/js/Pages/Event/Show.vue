<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

import Play from '../Event/Partials/Play.vue';
import End from '../Event/Partials/End.vue';
import EndGame from '../Event/Partials/EndGame.vue';

import { formatDate } from '@/utils';

const props = defineProps({
    game: Object,
    questions: Object,
    answers: Object,
    round: Number,
    questionNumber:Number,
    routeName: String,
    error: String
});

const flashMessage = ref(null);
const fadeOut = ref(false);

</script>

<template>
    <template v-if="routeName === 'games.startGame' || routeName === 'games.startRound'">
        <Play :questions="questions" :round="round" :game="game" :questionNumber="questionNumber" />
    </template>
    <template v-if="routeName === 'games.endRound'">
        <End :answers="answers" :round="round" :game="game" :questionNumber="questionNumber" />
    </template>
    <template v-if="routeName === 'games.endGame'">
        <EndGame :game="game" />
    </template>
</template>
