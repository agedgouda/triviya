<script setup>
import { ref,computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PlayerQuestions from './PlayerQuestions.vue';
import LandingPage from './LandingPage.vue';
import LandingPage2 from './LandingPage2.vue';
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
const thePage = ref('landing1');
//const answeredCount = computed(() => props.questions.filter(q => q.answer !== null).length);
//thePage.value =  (pageProps.auth.user && pageProps.auth.user.id) ===  props.user.id ? 'questions' : 'landing1';

</script>

<template>

    <div v-if="'landing1' === thePage" >
        <LandingPage :game="game" @nextClicked="thePage = 'landing2'"/>
    </div>
    <div v-if="answeredCount === 0 && 'landing2' === thePage" >
        <LandingPage2 :game="game" :user="user"  @nextClicked="thePage = 'questions'"/>
    </div>

    <div v-if="answeredCount > 0  || 'questions' === thePage">
        <PlayerQuestions :questions="questions" :game="game" :user="user" />
    </div>
</template>
