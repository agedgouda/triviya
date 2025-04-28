<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { formatDate } from '@/utils';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
});

const { props: pageProps } = usePage();


</script>

<template>
    <QuestionsLayout title="Questions" :header-has-background="false">
        <template #header>
            <ApplicationLogo class="flex justify-center block !h-20 mx-auto w-auto" />
        </template>

        <template #question-header>
            <div class="mb-2 text-center text-3xl font-bold">Welcome {{user.name}}</div>
            <div class="text-center text-lg">Here are your questions for the game Jamie Kramer is hosting at<br/>{{game.location}} on {{ formatDate(game.date_time) }}.</div>
        </template>

        <template #question-input>
            <div class="mb-2 text-center text-3xl font-bold">A Reminder</div>
            <ul class="list-disc mx-12 mb-2">
                <li>Trivius is the game where your answers become the trivia.</li>
                <li>The answers you give in the survey below becomes part of the game later.</li>
                <li>Once you’ve completed the quiz, you’ll be asked to complete your account registration. Creating an account is FREE and will allow you to change your questions up until game day, plus enables you to create your own Trivius games later!</li>
            </ul>
        </template>

        <template #question-footer>
            The next step is answering your TriviYa survey and should take less than 3 minutes to complete.
        </template>

        <template #question-buttons>
            <PrimaryButton type="button" class="mt-2" @click="$emit('start')">
                <span>Start</span>
            </PrimaryButton>
        </template>
    </QuestionsLayout>
</template>
