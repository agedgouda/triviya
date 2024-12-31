<script setup>
import { ref } from 'vue';
import { formatDate } from '@/utils';
import { usePage } from '@inertiajs/vue3';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';
import PlayerQuestions from '@/Components/PlayerQuestions.vue';
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
showWelcome.value =  (pageProps.auth.user && pageProps.auth.user.id) ===  props.user.id ? false : true;

</script>

<template>

    <div v-if="showWelcome">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-triviusBlue text-white">
            <div class="pt-3 text-center max-w-2xl">
                <div class="mb-4">
                    <ApplicationLogo class="flex justify-center block h-24 mx-auto w-auto mb-5" />
                    <h1 class="text-2xl font-bold">The party game where you are the trivia!</h1>
                    <p class="mb-4 text-xl">{{ game.host[0].first_name }} {{ game.host[0].last_name }} has invited you to play Trivius</p>
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

    <div v-else>
        <QuestionsLayout title="Questions">
            <template #header>
                <ApplicationLogo class="flex justify-center block h-24 mx-auto w-auto mb-5" />
                <div> Questions for {{game.name}}</div>

            </template>
                <div class="text-base mt-5 mb-5">
                    Welcome, {{ user.first_name }} {{ user.last_name }}!
                    <p>
                        Here are your questions for the game
                        {{ game.host[0].first_name }} {{ game.host[0].last_name }} is hosting at {{ game.location }}
                        on {{ formatDate(game.date_time) }}.

                        <div v-if="user.has_registered && !$page.props.auth.user" class="mt-3">
                            Once you’ve completed the quiz, you will be asked to log into the system. Once you've logged in you can review and
                            update your answers as well as see who else is playing.
                        </div>
                        <div v-if="!user.has_registered">
                            Once you’ve completed the quiz, you will be asked to complete your account registration.
                            Creating an account is free, and will let you change your questions up until game day.
                            It also enables you to make your own Trivius Game!
                        </div>
                    </p>
                </div>

                <PlayerQuestions :questions="questions" :answers="answers" :game="game" :user="user" />

        </QuestionsLayout>
    </div>
</template>
