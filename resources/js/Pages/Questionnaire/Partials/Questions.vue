<script setup>
import { ref,computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PlayerQuestions from './PlayerQuestions.vue';
// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
    answers: Array,
});

const { props: pageProps } = usePage();
//const answeredCount = computed(() => props.questions.filter(q => q.answer !== null).length);
//thePage.value =  (pageProps.auth.user && pageProps.auth.user.id) ===  props.user.id ? 'questions' : 'landing1';

</script>

<template>
    <div v-if="answeredCount > 0  || 'questions' === thePage">
        <PlayerQuestions :questions="questions" :game="game" :user="user" />
    </div>
</template>
