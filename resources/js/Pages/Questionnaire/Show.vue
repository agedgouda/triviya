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
    playerToken: String,
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

// Remember this guest player on this device (id plus the server-issued token)
// so they can resume without re-entering their name.
const storageKey = computed(() => `game_${props.game.id}`);
let attemptedResume = false;

const readSavedPlayer = () => {
    try {
        const saved = JSON.parse(localStorage.getItem(storageKey.value));
        if (saved && saved.id && saved.token) return saved;
    } catch (e) {
        // Older entries stored only the id; they can no longer be used to resume.
    }
    localStorage.removeItem(storageKey.value);
    return null;
};

watch(
    () => [props.user, props.playerToken, props.routeName],
    ([user, playerToken, routeName]) => {
        if (user && playerToken) {
            localStorage.setItem(storageKey.value, JSON.stringify({ id: user.id, token: playerToken }));
            return;
        }

        if (!user && routeName === 'questions.showQuestionLanding' && !attemptedResume) {
            if (new URLSearchParams(window.location.search).has('player')) {
                // A resume was already tried and the server rejected it.
                localStorage.removeItem(storageKey.value);
                return;
            }

            const saved = readSavedPlayer();

            if (saved) {
                attemptedResume = true;
                const params = new URLSearchParams({ player: saved.id, token: saved.token });
                router.visit(`${window.location.pathname}?${params}`, {
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

