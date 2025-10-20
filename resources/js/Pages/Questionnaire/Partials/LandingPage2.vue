<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
});

const hasAnyAnswer = computed(() => props.questions.some(q => !!q.answer));


</script>

<template>
    <QuestionsLayout title="Questions" :header-has-background="false">

        <template #raw-header>
            <div class="raw-header mt-8 mb-8">
                <h3 class="game-author text-center text-white font-bold text-xl" v-text="game.host[0].name"></h3>
                <h4 class="text-center text-white text-lg">has invited you to play</h4>
                <div class="extended-logo">
                    <ApplicationLogo class="flex justify-center block !h-32 mx-auto w-auto" />
                    <div class="extended-logo-text text-center text-white text-xl font-bold">the game where you<br />are the trivia!</div>
                </div>
            </div>
        </template>

        <template #question-input>
            <div class="mb-2 text-center text-xl font-bold border-b-2 pb-4" v-if="!hasAnyAnswer">Congrats {{ user.first_name }} - You're In!</div>
            <div class="mb-2 text-center text-xl font-bold border-b-2 pb-4" v-else>Welcome Back {{ user.first_name }}</div>
            <ul class="list-disc mx-12 mb-2">
                <li class="mb-2"  v-if="!hasAnyAnswer">Answer 10  questions - have fun and don't overthink it.</li>
                <li class="mb-2">You can edit your answers anytime until the game begins.</li>
            </ul>
            <div class="mb-2 text-center b-4">
                <SecondaryButton type="button" class="mt-2 mr-3" @click="router.visit(route('games.show', game.id))" v-if="hasAnyAnswer">
                    <span>Back</span>
                </SecondaryButton>
                <PrimaryButton type="button" class="mt-2" @click="$emit('start-game')">
                    <span v-if="!hasAnyAnswer">Start</span>
                    <span v-else>Edit</span>
                </PrimaryButton>
            </div>
        </template>
    </QuestionsLayout>
</template>
