<script setup>

import Questions from './Partials/Questions.vue';
import ThankYou from './Partials/ThankYou.vue';

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
    <Questions :questions="questions" :game="game" :user="user" v-if="routeName === 'questions.showQuestions'"/>
    <ThankYou :game="game" :user="user" v-if="routeName === 'questions.showThankYou'"/>
</template>
