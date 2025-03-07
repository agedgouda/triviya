<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';
import PlayerQuestions from './PlayerQuestions.vue';
import { formatDate } from '@/utils';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
});


</script>

<template>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-triviusBlue text-white">
            <div class="pt-3 text-center max-w-2xl">
                <div class="mb-4">
                    <ApplicationLogo class="flex justify-center block h-24 mx-auto w-auto" />
                    <h1 class="mb-4 text-2xl font-bold">The party game
                        <span class="hidden sm:inline">where </span>
                        <span class="inline sm:hidden">where<br></span>
                        you are the trivia!
                    </h1>
                    <p class="mb-4 text-xl">You've answered alll of the questions for {{ game.host[0].first_name }} {{ game.host[0].last_name }}'s Trivius
                        game on {{  formatDate(game.date_time)  }} at {{ game.location }}
                    </p>

                    We are going to put text here. Good text. Text better than you've ever seen before. You'l read it and say "wow, that is some good text."
                    All to get you to click below to <span> {{ user.password ? 'Login': 'Register'}} </span>
                    <div class="mt-4">
                        <SecondaryButton type="submit" class="mt-4 mb-4 ml-4">{{ user.password ? 'Login': 'Register'}}</SecondaryButton>
                    </div>
                </div>
            </div>
        </div>
</template>
