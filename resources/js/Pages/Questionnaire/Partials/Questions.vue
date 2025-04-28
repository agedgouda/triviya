<script setup>
import { ref,computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PlayerQuestions from './PlayerQuestions.vue';
import LandingPage from './LandingPage.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
    answers: Array,
});

const { props: pageProps } = usePage();
const showWelcome = ref(true);
const answeredCount = computed(() => props.questions.filter(q => q.answer !== null).length);
showWelcome.value =  (pageProps.auth.user && pageProps.auth.user.id) ===  props.user.id ? false : true;

</script>

<template>
    <div v-if="answeredCount === 0 && showWelcome" >
        <LandingPage :game="game" :user="user"  @start="showWelcome = false"/>
    </div>

    <div v-if="answeredCount > 0  || !showWelcome">
        <PlayerQuestions :questions="questions" :game="game" :user="user" />
    </div>
</template>
