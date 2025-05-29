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

    // console.log(props);

</script>

<template>
    <QuestionsLayout title="Questions" :header-has-background="false">
<!--        <template #header>-->
<!--            <ApplicationLogo class="flex justify-center block !h-20 mx-auto w-auto" />-->
<!--        </template>-->

        <template #raw-header>
            <div class="raw-header mt-8 mb-4">
                <h3 class="game-author text-center text-white font-bold text-xl" v-text="game.host[0].name"></h3>
                <h4 class="text-center text-white text-lg">has invited you to play</h4>
                <div class="extended-logo">
                    <ApplicationLogo class="flex justify-center block !h-32 mx-auto w-auto" />
                    <div class="extended-logo-text text-center text-white text-xl font-bold">the game where you<br />are the trivia!</div>
                </div>
            </div>
        </template>

        <template #question-header>
            <div class="mb-2 text-xl font-bold">Welcome {{user.name}}</div>
            <div class="text-lg mb-2">You’ll answer 10 questions that become the trivia in the game.  Shhh... don’t share your answers—or the questions.</div>
            <div class="text-lg">And whatever you do, DON’T overthink it.</div>
        </template>

<!--        <template #question-input>-->
<!--            <div class="mb-2 text-center text-3xl font-bold">A Reminder</div>-->
<!--            <ul class="list-disc mx-12 mb-2">-->
<!--                <li>Trivius is the game where your answers become the trivia.</li>-->
<!--                <li>The answers you give in the survey below becomes part of the game later.</li>-->
<!--                <li>Once you’ve completed the quiz, you’ll be asked to complete your account registration. Creating an account is FREE and will allow you to change your questions up until game day, plus enables you to create your own Trivius games later!</li>-->
<!--            </ul>-->
<!--        </template>-->

<!--        <template #question-footer>-->
<!--            The next step is answering your TriviYa survey and should take less than 3 minutes to complete.-->
<!--        </template>-->

        <template #question-buttons>
            <PrimaryButton type="button" class="mt-2" @click="$emit('nextClicked')">
                <span>Next</span>
            </PrimaryButton>
        </template>
    </QuestionsLayout>
</template>
