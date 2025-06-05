<script setup>
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
});

const { props: pageProps } = usePage();

const login = () => {
    router.visit(route('login.prepopulated', props.game.id));
};



</script>

<template>
    <QuestionsLayout title="Questions" :header-has-background="false">
<!--        <template #header>-->
<!--            <ApplicationLogo class="flex justify-center block !h-20 mx-auto w-auto" />-->
<!--        </template>-->

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

<!--        <template #question-header>-->
<!--            <div class="mb-2 text-center text-3xl font-bold">Welcome {{user.name}}</div>-->
<!--            <div class="text-center text-lg">Here are your questions for the game Jamie Kramer is hosting at<br/>{{game.location}} on {{ formatDate(game.date_time) }}.</div>-->
<!--        </template>-->

<!--        <template #question-input>-->
<!--            <div class="mb-2 text-center text-3xl font-bold">A Reminder</div>-->
<!--            <ul class="list-disc mx-12 mb-2">-->
<!--                <li>Trivius is the game where your answers become the trivia.</li>-->
<!--                <li>The answers you give in the survey below becomes part of the game later.</li>-->
<!--                <li>Once you’ve completed the quiz, you’ll be asked to complete your account registration. Creating an account is FREE and will allow you to change your questions up until game day, plus enables you to create your own Trivius games later!</li>-->
<!--            </ul>-->
<!--        </template>-->

        <template #question-input>
            <div class="mb-2 text-center text-xl font-bold border-b-2 pb-4">Here's how it works</div>
            <ul class="list-disc mx-12 mb-2">
                <li class="mb-2">Everyone invited to the game will answer 10 questions about themselves</li>
                <li class="mb-2">During the game, teams will guess who said what</li>
                <li class="mb-2">Teams earn points matching the answers to the correct people</li>
                <li class="mb-2">The team with the most points wins!</li>
                <li>You must login or register to begin.</li>
            </ul>
            <div class="mb-2 text-center b-4">
                <PrimaryButton type="button" class="mt-2" @click="login">
                    <span>Login</span>
                </PrimaryButton>
                <PrimaryButton type="button" class="mt-2 ml-2" @click="$emit('nextClicked')">
                    <span>Register</span>
                </PrimaryButton>
            </div>
        </template>

    </QuestionsLayout>
</template>
