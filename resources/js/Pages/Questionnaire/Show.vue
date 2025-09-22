<script setup>
import { computed, onMounted } from 'vue';
import ThankYou from './Partials/ThankYou.vue';
import LandingPage from './Partials/LandingPage.vue';
import PlayerQuestions from './Partials/PlayerQuestions.vue';

import Flash from '@/Components/Flash.vue';
import { useFlash } from '@/Composables/useFlash';

const { setFlash } = useFlash();
// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
    answers: Array,
    routeName: String,
    flashMessage: String,
});

onMounted(() => {
  if (props.flashMessage) {
    setFlash(props.flashMessage)
  }
})



</script>

<template>
    <Flash />
    <LandingPage :game="game" :user="user" v-if="routeName === 'questions.showQuestionLanding'"/>
    <PlayerQuestions :questions="questions" :game="game" :user="user"  v-if="routeName === 'questions.showQuestions'"/>
    <ThankYou :game="game" :user="user" v-if="routeName === 'questions.showThankYou'"/>
</template>

