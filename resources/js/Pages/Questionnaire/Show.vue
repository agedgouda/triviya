<script setup>
import { computed } from 'vue';
import ThankYou from './Partials/ThankYou.vue';
import LandingPage from './Partials/LandingPage.vue';
import PlayerQuestions from './Partials/PlayerQuestions.vue';


// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
    answers: Array,
    routeName: String,
});

</script>

<template>
    <LandingPage :game="game" :user="user" v-if="routeName === 'questions.showQuestionLanding'"/>
    <PlayerQuestions :questions="questions" :game="game" :user="user"  v-if="routeName === 'questions.showQuestions'"/>
    <ThankYou :game="game" :user="user" v-if="routeName === 'questions.showThankYou'"/>
</template>

