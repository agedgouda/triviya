<script setup>
import { ref } from 'vue';
import { formatDate } from '@/utils';
import { usePage } from '@inertiajs/vue3';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';
import PlayerQuestions from '@/Components/PlayerQuestions.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
    answers: Array,
});

const { props: pageProps } = usePage();
const showWelcome = ref(true);
showWelcome.value =  pageProps.auth.user.id ===  props.user.id ? false : true;
</script>

<template>

    <div v-if="showWelcome">
        <QuestionsLayout title="Questions">
            <div class="flex justify-center">
                <div class="pt-3 text-center max-w-2xl">
                    <div class="mb-4">
                        <ApplicationLogo class="flex justify-center block h-24 mx-auto w-auto mb-5" />
                        <h1 class="text-lg font-bold">Welcome to Trivus</h1>
                        <p class="mb-4">The party game where you are the trivia!</p>
                        <ul class="list-disc text-left inline-block">
                            <li>Answer a few fun questions about yourself. You’ll have to register to save your answers.</li>
                            <li>This way, you can change them right up until party time.</li>
                            <li>Everyone invited to the party will do the same.</li>
                            <li>Then, at the party, teams will compete to guess who said what.</li>
                            <li>Match people to answers and score points.</li>
                            <li>The team with the highest score takes home the trophy.</li>
                        </ul>
                        <div class="mt-4">
                            <a href="#" class="text-pink-600 cursor:pointer" @click="showWelcome = false">Click here to get started</a>
                        </div>
                    </div>
                </div>
            </div>
        </QuestionsLayout>

    </div>

    <div v-else>
        <QuestionsLayout title="Questions">
            <template #header>
                <div>{{game.name}}</div>
                <div class="text-base">Hosted by {{ game.host[0].first_name }} {{ game.host[0].last_name }}</div>
                <div class="text-base">{{ formatDate(game.date_time) }}</div>
                <div class="text-base">{{ game.location }}</div>
            </template>

            <div class="pl-5 pt-3">
                <div class="ml-4 mb-4">
                    Welcome, {{ user.first_name }} {{ user.last_name }}.
                    <div v-if="!user.has_registered">
                        Once you’ve completed the quiz, please create an account to save and change your answers at any time before the party.
                        Creating an account also enables you to make your own Trivius Game at any time.
                    </div>
                </div>
                <PlayerQuestions :questions="questions" :answers="answers" :game="game" :user="user" />
            </div>
        </QuestionsLayout>
    </div>
</template>
