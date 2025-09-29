<script setup>
import { onMounted,computed } from 'vue';
import ThankYou from './Partials/ThankYou.vue';
import LandingPage from './Partials/LandingPage.vue';
import LandingPageClosed from './Partials/LandingPageClosed.vue';
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

const currentLandingComponent = computed(() => {
    if (!props.game.is_full && ['new', 'ready'].includes(props.game.status)) {
        if (props.routeName !== 'questions.showQuestionLanding') return null;
        return LandingPage;
    } else {
        return LandingPageClosed;
    }
});

onMounted(() => {
  if (props.flashMessage) {
    setFlash(props.flashMessage)
  }
})


</script>

<template>
    <Flash />
    <component
        :is="currentLandingComponent"
        v-if="currentLandingComponent"
        :game="game"
        :user="user"
    />

    <PlayerQuestions :questions="questions" :game="game" :user="user"  v-if="routeName === 'questions.showQuestions'"/>
    <ThankYou :game="game" :user="user" v-if="routeName === 'questions.showThankYou'"/>
</template>

