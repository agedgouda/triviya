<script setup>
import { ref,computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PlayerQuestions from './PlayerQuestions.vue';
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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-triviusRegular text-white" style="background-image: url('/images/trivius-bg-cover.png');">
            <div class="pt-3 text-center max-w-2xl">
                <div class="mb-4">
                    <ApplicationLogo class="flex justify-center block h-24 mx-auto w-auto" />
                    <h1 class="mb-4 text-2xl font-bold">The party game
                        <span class="hidden sm:inline">where </span>
                        <span class="inline sm:hidden">where<br></span>
                        you are the trivia!
                    </h1>
                    <p class="mb-4 text-xl">{{ game.host[0].first_name }} {{ game.host[0].last_name }}
                        <span class="inline sm:hidden"><br></span>has invited you to play Trivius</p>
                    <ul class="list-disc text-left inline-block text-sm sm:text-lg px-10 sm:px-0">
                        <li>Answer a few fun questions about yourself.</li>
                        <li>You’ll have to register to save your answers so you can change them right up until party time.</li>
                        <li>Everyone invited to the party will do the same.</li>
                        <li>Then, at the party, teams will compete to guess who said what.</li>
                        <li>Match people to answers and score points.</li>
                        <li>The team with the highest score takes home the trophy.</li>
                    </ul>
                    <div class="mt-4">
                        <SecondaryButton type="submit" class="mt-4 mb-4 ml-4" @click="showWelcome = false">Get Started</SecondaryButton>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="answeredCount > 0  || !showWelcome">

        <PlayerQuestions :questions="questions" :game="game" :user="user" />

    </div>
</template>
