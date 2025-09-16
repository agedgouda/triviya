<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import GamesList from './Partials/GamesList.vue';
import GameDetails from './Partials/GameDetails.vue';
import GameEdit from './Partials/GameEdit.vue';
import PlayerQuestions from '@/Pages/Questionnaire/Partials/PlayerQuestions.vue';
import AllPlayerAnswers from './Partials/AllPlayerAnswers.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { formatDate } from '@/utils';
import { useFlash } from '@/Composables/useFlash';

const props = defineProps({
    games: Object,
    game: Object,
    questions: Object,
    host: Object,
    players: Array,
    routeName: String,
    modes: Array,
    error: String
});

// Flash handling
const { flashMessage, fadeOut, setFlash } = useFlash();

// Compute props for dynamic components
const currentProps = computed(() => {
    switch (props.routeName) {
        case 'games':
            return { games: props.games };
        case 'games.show':
            return { game: props.game, players: props.players };
        case 'games.create':
            return { modes: props.modes, routeName: props.routeName };
        case 'games.edit':
            return { modes: props.modes, game: props.game, routeName: props.routeName };
        case 'games.showQuestions':
            return { questions: props.questions, game: props.game, user: $page.props.auth.user };
        case 'games.showAnswers':
            return { questions: props.questions };
        default:
            return {};
    }
});

// Map routes to components
const routeComponents = {
    'games': GamesList,
    'games.show': GameDetails,
    'games.create': GameEdit,
    'games.edit': GameEdit,
    'games.showQuestions': PlayerQuestions,
    'games.showAnswers': AllPlayerAnswers
};

const CurrentComponent = computed(() => routeComponents[props.routeName] || null);

const navigate = (routeName) => {
    router.visit(route(routeName));
};

</script>

<template>
    <AppLayout title="Games">
        <template #header>
            <div v-if="props.routeName === 'games.showQuestions'">
                <div>{{ props.game.name }}</div>
                <div class="text-base">
                    Hosted by {{ props.game.host[0].first_name }} {{ props.game.host[0].last_name }}
                </div>
                <div class="text-base">{{ formatDate(props.game.date_time) }}</div>
                <div class="text-base">{{ props.game.location }}</div>
            </div>
        </template>

        <div class="mx-5">
            <!-- Dynamic page content -->
            <component :is="CurrentComponent" v-bind="currentProps" />

            <!-- Special buttons for games routes -->
            <div v-if="props.routeName === 'games'">
                <div v-if="props.games.data.length === 0" class="mb-5 text-center">
                    <div class="mb-1 font-bold">Let’s Do This!</div>
                    <div class="mb-2">Click new game to get started</div>
                    <PrimaryButton @click="navigate('games.create')">New Game</PrimaryButton>
                </div>
                <div v-else class="flex justify-end my-5">
                    <PrimaryButton @click="navigate('games.create')">Create A New Game</PrimaryButton>
                </div>
            </div>

            <div v-if="props.routeName === 'games.show'" class="flex justify-end my-5">
                <PrimaryButton @click="navigate('games')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    &nbsp;Back To All Games
                </PrimaryButton>
            </div>

        </div>
    </AppLayout>
</template>
