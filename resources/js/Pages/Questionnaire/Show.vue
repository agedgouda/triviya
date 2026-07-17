<script setup>
import { onMounted, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
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
    if (!props.game.is_full && ['new', 'ready', 'sequel'].includes(props.game.status)) {
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

// Remember this player on this device so they can resume without re-entering
// their name, and resume automatically if we already know who they are.
const storageKey = computed(() => `game_${props.game.id}`);
let attemptedResume = false;

watch(
    () => [props.user, props.routeName],
    ([user, routeName]) => {
        if (user) {
            localStorage.setItem(storageKey.value, user.id);
            return;
        }

        if (routeName === 'questions.showQuestionLanding' && !attemptedResume) {
            const savedPlayerId = localStorage.getItem(storageKey.value);

            if (savedPlayerId) {
                attemptedResume = true;
                router.visit(`${window.location.pathname}?player=${savedPlayerId}`, {
                    replace: true,
                    preserveState: false,
                });
            }
        }
    },
    { immediate: true }
);

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

