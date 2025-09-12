<script setup>
import { formatDate } from '@/utils';
import { router } from '@inertiajs/vue3';
import { ref,computed } from 'vue';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Table from '@/Components/Table.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    game: Object,
    players: Array,
});

const processing = ref(false);

const currentDomain = window.location.origin;

const page = usePage();
const host = page.props.host;
const hostName = host.first_name+' '+host.last_name;

const questionsAnsweredCount = computed(() =>
  props.players.filter(player => (player.status === 'Questions Answered' || player.status === 'Quiz Complete')).length
);

const goToEditPage = () => {
    router.visit(route('games.edit', props.game.id));
};

const handlePlayerAction = async (player, action, method = 'put', additionalParams = {}) => {
    try {
        // Define the route dynamically based on the action
        const routes = {
            updateAttendance: 'games.updateAttendance',
            sendQuestions: 'games.sendQuestions',
            removePlayer:'games.removePlayer'
        };

        // Ensure the action is valid
        if (!routes[action]) {
            throw new Error(`Invalid action: ${action}`);
        }

        // Show confirmation dialog if removing a player
        if (action === 'removePlayer') {
            const confirmed = confirm(`Are you sure you want to remove ${player.email} from the game?`);
            if (!confirmed) return;
        }

        // Construct the route with additional parameters
        const response = await axios[method](
            route(routes[action], { game: props.game.id, user: player.id, ...additionalParams })
        );

        // Handle success response
        if (response.data.status === 'success') {
            // Update the player's status in the UI
            player.status = response.data.message;
            if(response.data.message === 'Questions Sent') {
                router.visit(route('games.showQuestions', { game: props.game.id, user: player.id }));
            }
            if(response.data.message === 'User removed'){
                const index = props.players.findIndex(player => player.status === "User removed");
                if (index !== -1) props.players.splice(index, 1);
                props.game.status = response.data.game_status;
            }

        } else {
            // Handle a failure response
            console.error(`Action '${action}' failed:`, response.data.message);
        }
    } catch (error) {
        // Handle errors
        console.error(`Error performing action '${action}':`, error);
        return {
            success: false,
            message: error.response?.data?.message || `Failed to perform action '${action}'.`,
        };
    }
};

const startGame = () => {
    router.visit(route('games.startGame', { game: props.game.id }));
};

const copyToClipboard = (game   ) => {
    const invitationUrl = `I’m hosting a game of TriviYa and you’re invited.\n\nAnswer 10 quick questions — then together we’ll compete to guess who said what.\n\nJoin here:\n${currentDomain}/questions/${game.id}\n\nSent by your host ${hostName} — TriviYa only gets your info once you register.`;
    if (navigator.clipboard && window.isSecureContext) {
        // Preferred method (only works in secure contexts)
        navigator.clipboard.writeText(invitationUrl)
        .then(() => alert('Invitation link copied to clipboard'))
        .catch(err => console.error('Failed to copy:', err));
    } else {
        // Fallback for insecure contexts
        const textArea = document.createElement('textarea');
        textArea.value = invitationUrl;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Invitation link copied to clipboard');
    }
};

</script>

<template>
    <div class="flex justify-between items-start gap-4">
        <!-- Left column with game info and edit button -->
        <div class="flex gap-4">
            <!-- Left column: stacked name + location -->
            <div class="flex flex-col justify-center">
                <div class="text-3xl text-triviyaRegular font-bold">{{ game.name }}</div>
                <div class="text-lg font-bold text-triviyaRegular">Location: {{ game.location }}</div>
            </div>

            <!-- Right column: vertically centered button -->
            <div v-if="$page.props.auth.user.id === host.id">
                <PrimaryButton @click="goToEditPage" class="ml-2 mt-7 pl-1 pr-1 pt-1 pb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="h-3 w-3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5
                            4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5
                            4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                </PrimaryButton>
            </div>
        </div>

        <!-- Right column with Start Game button -->
        <div class="flex justify-end mt-2" v-if="game.status === 'start' || game.status === 'in progress'  && $page.props.auth.user.id === host.id">
            <PrimaryButton @click="startGame">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9
                        9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0
                        1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9
                        9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                </svg>
                &nbsp;
                <span v-if="game.status === 'in progress'">Continue Playing</span>
                <span v-else>Start Game</span>
            </PrimaryButton>
        </div>
        <div class="flex-1 flex justify-center mt-2 text-4xl font-bold" v-if="game.status.includes('done') ">
            GAME COMPLETE
        </div>
    </div>

    <div v-if="$page.props.auth.user.id === host.id" >
        <div class="mt-4">
            <div v-if="players.length < 4">
                You're the host of TriviYa! You'll need to:
                <ul class="list-disc pl-4 ml-0">
                    <li>Invite at least {{4 - players.length}} more players to join.</li>
                    <li>Share your unique game link via email, text or group chat, whatever works best for you</li>
                    <li>Players names and status appear here once they’ve registered</li>
                </ul>
            </div>
            <div v-if="players.length >= 4">
                <div v-if="questionsAnsweredCount < players.length">
                    Still waiting for {{ players.length - questionsAnsweredCount }} more player<span v-if="players.length - questionsAnsweredCount > 1">s</span> to answer their questions before the game can begin.
                </div>
            </div>
            <div v-if="game.status === 'new' || game.status === 'start'" class="mt-3 flex flex-col gap-2">
                <div class="font-bold text-triviyaRegular italic">
                    TriviYa doesn’t send invites or store players emails – you control who gets your link.
                </div>
                <div>
                    <SecondaryButton @click="copyToClipboard(game)">
                        &nbsp;Copy Link
                    </SecondaryButton>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <span class="text-red-800">{{ $page.props.errors.msg }}</span>
        <Table class="min-w-full table-auto ">
            <template #header>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-center">Questions</th>
                    <th class="px-4 py-2 text-center"></th>
            </template>
            <template #default="{ rowClass }">
            <tr v-for="player in players"
                :key="player.id"
                :class="[
                    rowClass
                ]"
            >
                <td class="px-4 py-2 flex items-center space-x-2  ">
                    <img class="w-8 h-8 rounded-full object-cover border-triviyaRegular border-2" :src="player.profile_photo_url" alt="Player Photo" />
                    <span>{{ player.name }}</span>
                </td>

                <td class="px-4 py-2 text-center">
                    {{ player.status }}
                </td>
                <td class="px-4 py-2 text-center">
                    <PrimaryButton @click="($page.props.auth.user.id === player.id) && $inertia.visit(route('questions.showQuestions', { game: game.id, user: player.id }))"
                        v-if="$page.props.auth.user.id === player.id && game.status === 'new'"
                        :class="{'pulse-button': player.status === 'Quiz Available'}"
                        >
                        <span v-if="player.status === 'Quiz Available'">
                            Start Quiz
                        </span>
                        <span v-else-if="player.status !== 'Quiz Complete'">
                            Finish Quiz
                        </span>
                        <span v-else>
                            Review Quiz
                        </span>
                    </PrimaryButton>
                    <div v-if="$page.props.auth.user.id != player.id && $page.props.auth.user.id === host.id && (game.status === 'new' ||  game.status === 'start')">
                        <DangerButton @click="handlePlayerAction(player, 'removePlayer', 'delete')" class="ml-2" >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </DangerButton>
                    </div>

                </td>
            </tr>
        </template>
        </Table>
    </div>

</template>
<style scoped>


@keyframes pulse-scale {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); } /* adjust scale as needed */
}


@keyframes pulse-color {
  0%, 100% {
    background-color: #E63946; /* base color */
  }
  50% {
    background-color: #EC6ABA; /* highlight color */
  }
}

.pulse-button {
  animation: pulse-scale 1s ease-in-out infinite;
  /*animation: pulse-color 1s infinite;*/
}
</style>


