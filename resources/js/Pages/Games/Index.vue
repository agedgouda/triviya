<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import GamesList from './Partials/GamesList.vue';
import GameDetails from './Partials/GameDetails.vue';
import GameEdit from './Partials/GameEdit.vue';
import PlayerQuestions from './Partials/PlayerQuestions.vue';
import PlayerAnswers from './Partials/PlayerAnswers.vue';
import AllPlayerAnswers from './Partials/AllPlayerAnswers.vue';
import Invite from './Partials/Invite.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    games: Object,
    gamesHosted: Object,
    game: Object,
    questions: Object,
    answers: Object,
    host: Object,
    players: Array,
    routeName: String,
    modes: Array,
    error: String
});

const goBack = () => {
    router.visit(route('games'));
};

const createGame = () => {
    router.visit(route('games.create'));
};


</script>

<template>
    <AppLayout title="Games">
        <template #header>

                Games

        </template>

        <div class="p-5">

            <h1 class="text-center text-xl font-bold text">{{ $page.props.flash.message }}</h1>

            <template v-if="routeName === 'games'">
                <div v-if="gamesHosted.data.length" class="mb-5">
                    <div class="mx-5 mt-3 font-bold pb-3">Games You Are Hosting</div>
                    <GamesList :games="gamesHosted" />
                </div>
                <div v-if="games.data.length">
                    <div class="mx-5 mt-3 font-bold pb-3">Games You are Playing</div>
                    <GamesList :games="games " />
                </div>
                <div class="flex items-center justify-end mt-4">
                <DangerButton class="ml-10 m-5"  @click="createGame" >
                    New Game
                </DangerButton>
            </div>
            </template>
            <template v-if="routeName === 'games.show'">

            <GameDetails :game="game" :players="players"/>

            <Invite :gameId="game.id" :invtees="game.invitees"   v-if="$page.props.auth.user.id === $page.props.host.id" />

            <DangerButton class="ml-10 mb-5"  @click="goBack" >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                &nbsp;Back
            </DangerButton>

            </template>
            <template v-if="routeName === 'games.create'">
                <GameEdit :modes="modes" :routeName="routeName" />
            </template>
            <template v-if="routeName === 'games.edit'">
                <GameEdit :modes="modes" :game="game" :routeName="routeName"  />
            </template>
            <template v-if="routeName === 'games.showQuestions'">
                <PlayerAnswers :questions="questions" :answers="answers" :game="game" :routeName="routeName" v-if="game.host && game.host[0].id===$page.props.auth.user.id"/>
                <PlayerQuestions :questions="questions" :answers="answers" :game="game" :routeName="routeName" v-else />
            </template>
            <template v-if="routeName === 'games.showAnswers'">
                <AllPlayerAnswers :questions="questions"   />
            </template>
        </div>
    </AppLayout>
</template>
<style scoped>
/* You can add your custom styles here */
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 8px 12px;
}
th {
  background-color: #f4f4f4;
}
</style>
