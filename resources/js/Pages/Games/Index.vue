<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import GamesList from './Partials/GamesList.vue';
import GameDetails from './Partials/GameDetails.vue';
import GameEdit from './Partials/GameEdit.vue';
import Invite from './Partials/Invite.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    games: Object,
    gamesHosted: Object,
    game: Object,
    host: Object,
    players: Array,
    routeName: String,
    modes: Array
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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Games
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <template v-if="routeName === 'games'">
                        <div v-if="gamesHosted.data.length" class="mb-5"> 
                            <div class="mx-5 mt-2 font-bold pb-3">Games You Are Hosting</div>
                            <GamesList :games="gamesHosted" />
                        </div>
                        <div v-if="games.data.length"> 
                            <div class="mx-5 font-bold pb-3">Games You are Playing</div>
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
                </div>
            </div>
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