<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import GamesList from './Partials/GamesList.vue';
import GameDetails from './Partials/GameDetails.vue';
import GameEdit from './Partials/GameEdit.vue';
import Play from './Partials/Play.vue';
import End from './Partials/End.vue';
import PlayerQuestions from '@/Pages/Questionnaire/Partials/PlayerQuestions.vue';
import AllPlayerAnswers from './Partials/AllPlayerAnswers.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { formatDate } from '@/utils';

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

const flashMessage = ref(null);
const fadeOut = ref(false);

onMounted(() => {
            flashMessage.value = localStorage.getItem('flashMessage');
            if (flashMessage.value) {
                // Clear the message from localStorage after it's displayed
                localStorage.removeItem('flashMessage');
            }
            setTimeout(() => {
            fadeOut.value = true; // Trigger fade-out class
            setTimeout(() => {
                    flashMessage.value = null; // Remove message after fade-out
                }, 1000); // Match the fade-out duration (1 second)
            }, 2000); // 2 seconds before fade-out starts
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
            <div v-if="routeName === 'games.showQuestions'">
                <div >{{game.name}}</div>
                <div class="text-base">Hosted by {{ game.host[0].first_name }} {{ game.host[0].last_name }}</div>
                <div class="text-base">{{ formatDate(game.date_time) }}</div>
                <div class="text-base">{{ game.location }}</div>
            </div>
        </template>

        <div>

            <div class="mx-5">
                <div
                    v-if="flashMessage"
                    :class="['transition-opacity duration-1000', { 'opacity-0': fadeOut }]"
                >
                    {{ flashMessage }}
                </div>
                <div
                    v-if="$page.props.flash.message"
                    :class="['transition-opacity duration-1000', { 'opacity-0': fadeOut }]"
                >
                    {{ $page.props.flash.message }}
                </div>
                <template v-if="routeName === 'games'">
                    <div v-if="gamesHosted.data.length" class="mb-5">
                        <div class="mt-3 font-bold pb-3">Games You Are Hosting</div>
                        <GamesList :games="gamesHosted" />
                    </div>
                    <div v-if="games.data.length">
                        <div class="mt-3 font-bold pb-3">Games You are Playing</div>
                        <GamesList :games="games " />
                    </div>
                    <div class="flex items-center mb-5" v-if="gamesHosted.data.length === 0 && games.data.length === 0">
                        <span>Welcome to Trivius. Click on NEW GAME to get started.</span>
                        <PrimaryButton class="ml-4" @click="createGame">
                            New Game
                        </PrimaryButton>
                        </div>
                        <div v-else class="flex justify-end">
                            <PrimaryButton class="my-5"  @click="createGame" >
                                New Game
                            </PrimaryButton>
                        </div>
                </template>
                <template v-if="routeName === 'games.show'">

                <GameDetails :game="game" :players="players"/>

                <PrimaryButton class="my-5"  @click="goBack" >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    &nbsp;Back To All Games
                </PrimaryButton>

                </template>
                <template v-if="routeName === 'games.create'">
                    <GameEdit :modes="modes" :routeName="routeName" />
                </template>
                <template v-if="routeName === 'games.edit'">
                    <GameEdit :modes="modes" :game="game" :routeName="routeName"  />
                </template>
                <template v-if="routeName === 'games.showQuestions'">
                    <PlayerQuestions :questions="questions" :game="game" :user="$page.props.auth.user"/>
                </template>
                <template v-if="routeName === 'games.showAnswers'">
                    <AllPlayerAnswers :questions="questions"   />
                </template>
                <template v-if="routeName === 'games.startGame'">
                    <Play :questions="questions"   />
                </template>
                <template v-if="routeName === 'games.endGame' || routeName === 'games.endRound'">
                    <End :answers="answers"   />
                </template>
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
