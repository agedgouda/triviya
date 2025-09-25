<script setup>
import { router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
});

const login = () => {
    router.visit(route('login.prepopulated', props.game.id));
};
const register = () => {
    router.visit(route('register.prepopulated', props.game.id));
};

const join = () => {
    router.visit(route('questions.showQuestions', props.game.id));
};



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
            <div class="mb-2 text-center text-xl font-bold border-b-2 pb-4">Sorry, this game is locked</div>
            <div class="w-full max-w-md mx-auto">
                <div class="my-2" v-if="game.is_full">This game has reached it's 12-player limit. {{ game.is_full }}</div>
                <div class="my-2" v-else=>This game has already started</div>
                <ul class="list-disc mx-12 mb-2">
                    <li class="mb-2">If you’ve already joined this game, please log in to continue.</li>
                    <li class="mb-2">I you're unsure and need more details, contact {{ game.host[0].first_name }} for help.</li>
                </ul>
                Can't join one?<br/>
                No problem - you can create a new game anytime.
            </div>
            <div class="mb-2 text-center b-4" v-if="!user">
                <PrimaryButton type="button" class="mt-2" @click="login">
                    <span>Login</span>
                </PrimaryButton>
                <PrimaryButton type="button" class="mt-2 ml-2" @click="register">
                    <span>Register</span>
                </PrimaryButton>
            </div>
            <div class="mb-2 text-center b-4" v-else>
                <PrimaryButton type="button" class="mt-2" @click="join">
                    <span>Join Game</span>
                </PrimaryButton>
            </div>
        </template>

    </QuestionsLayout>
</template>
