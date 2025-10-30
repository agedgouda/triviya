<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed,onMounted } from 'vue';
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
    inviteMessage: String,
    flashMessage: String,
    error: String
});

// Flash handling
const { setFlash } = useFlash();

// Compute props for dynamic components
const currentProps = computed(() => {
    switch (props.routeName) {
        case 'games':
            return { games: props.games };
        case 'games.show':
            return { game: props.game, players: props.players , inviteMessage: props.inviteMessage };
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

onMounted(() => {
  if (props.flashMessage) {
    setFlash(props.flashMessage)
  }
})


</script>

<template>
    <AppLayout title="Games">

        <div class="mx-5 sm:mx-1">
            <component :is="CurrentComponent" v-bind="currentProps" />
        </div>
    </AppLayout>
</template>
