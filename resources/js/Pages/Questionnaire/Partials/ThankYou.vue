<script setup>
import { router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
});

const goBack = () => {
    if (props.user.is_guest) {
        router.visit(route('questions.showQuestions', { game: props.game.id, player: props.user.id }));
    } else {
        router.visit(route('games.show', { game: props.game.id }));
    }
};

</script>

<template>
    <QuestionsLayout title="Well Done!" :header-has-background="false">

        <template #raw-header>
            <div class="raw-header mt-8 mb-8">
                <div class="extended-logo">
                    <ApplicationLogo class="flex justify-center block !h-32 mx-auto w-auto" />
                    <div class="extended-logo-text text-center text-white text-xl font-bold">the game where you<br />are the trivia!</div>
                </div>
            </div>
        </template>

        <template #question-input>
            <div class="mb-2 text-center text-xl font-bold border-b-2 pb-4">Well Done!</div>
            <div class="mb-2 mt-2 ">
                <p class="mb-2 mt-2 ">You're all set! TriviYA is working its magic, turning everone's answers into your custom trivia game.</p>
                <p>When the game starts, the host will ask the questions? Your job? Guess who said what!</p>
            </div>
            <div class="text-center">
                    <PrimaryButton type="submit" class="mt-4 mb-4 ml-4" @click="goBack">
                        <span v-if="user.is_guest">Review My Answers</span>
                        <span v-else>View Dashboard</span>
                    </PrimaryButton>
            </div>
        </template>

    </QuestionsLayout>
</template>
