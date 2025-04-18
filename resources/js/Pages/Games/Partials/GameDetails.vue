<script setup>
import { formatDate } from '@/utils';
import { router } from '@inertiajs/vue3';
import { ref,computed } from 'vue';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Invite from './Invite.vue';
import Table from '@/Components/Table.vue';

const props = defineProps({
    game: Object,
    players: Array,
});

const processing = ref(false)

const currentDomain = window.location.origin;

const questionsAnsweredCount = computed(() =>
  props.players.filter(player => player.status === 'Questions Answered').length
);

const goToEditPage = () => {
    router.visit(route('games.edit', props.game.id));
};

const handlePlayerAction = async (player, action, method = 'put', additionalParams = {}) => {
    try {
        // Define the route dynamically based on the action
        const routes = {
            resendInvite: 'games.resend-invite',
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

const copyToClipboard = (game, player) => {
    const invitationUrl = currentDomain+'/questions/'+game.id+'/'+player.id
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
    <div class="text-lg font-bold">{{ game.name }}</div>
    <!--<div><span class="font-bold">Mode: </span>{{ game.mode.name }}</div>-->
    <div><span class="font-bold">Location: </span>{{ game.location }}</div>
    <div>{{ formatDate(game.date_time) }}</div>
    <div v-if="$page.props.auth.user.id === $page.props.host.id" >

        <div>
            <PrimaryButton @click="goToEditPage" class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                </svg>

                &nbsp;Edit
            </PrimaryButton>
        </div>

        <div class="mt-34">
            <div v-if="players.length < 4">
                TriviYa is best played with a group! The host (that’s you) has automatically been added. <span v-if="$page.props.host.pivot.status !== 'Questions Answered'">Click ‘TAKE QUIZ’ below to get started.</span>
                <p>You’ll need at least {{4 - players.length}} more player<span v-if="players.length < 3">s</span> before the game can begin.</p>
            </div>
            <div v-if="players.length >= 4">
                <div v-if="questionsAnsweredCount < players.length">
                    Still waiting for {{ players.length - questionsAnsweredCount }} more player<span v-if="players.length - questionsAnsweredCount > 1">s</span> to answer their questions before the game can begin.
                </div>
                <DangerButton @click="startGame" v-if="questionsAnsweredCount === players.length">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                    </svg>
                    &nbsp;Start Game
                </DangerButton>
            </div>
        </div>
    </div>
    <Invite :gameId="game.id" v-if="$page.props.auth.user.id === $page.props.host.id" />
    <div class="mt-5">
        <span class="text-red-800">{{ $page.props.errors.msg }}</span>
        <Table class="min-w-full table-auto ">
            <template #header>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left" v-if="$page.props.auth.user.id === $page.props.host.id">Email</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-center" v-if="$page.props.auth.user.id === $page.props.host.id"></th>
                    <th class="px-4 py-2 text-center" v-if="$page.props.auth.user.id === $page.props.host.id"></th>
            </template>
            <template #default="{ rowClass }">
            <tr v-for="player in players"
                :key="player.id"
                :class="[
                    rowClass,
                    $page.props.auth.user.id === player.id ? 'bg-triviyaLight cursor-pointer text-red-600' : ''
                ]"
                @click="($page.props.auth.user.id === player.id) && $inertia.visit(route('questions.showQuestions', { game: game.id, user: player.id }))"
            >
            <td class="px-4 py-2 flex items-center space-x-2">
                <img class="w-8 h-8 rounded-full object-cover" :src="player.profile_photo_url" alt="Player Photo" />
                <span>{{ player.name }}</span>
            </td>
                <td class="px-4 py-2 text-left" v-if="$page.props.auth.user.id === $page.props.host.id">{{ player.email }}</td>
                <td class="px-4 py-2 text-center" v-if="$page.props.auth.user.id === player.id && player.status === 'Questions Answered'">
                    Review & Edit<br/>Your Answers
                </td>
                <td class="px-4 py-2 text-center" v-else>
                    {{ player.status }}
                </td>
                <td class="px-4 py-2 text-center" v-if="$page.props.auth.user.id === $page.props.host.id && player.id != $page.props.host.id" >
                    <div>
                        <SecondaryButton @click="copyToClipboard(game, player)" v-if="player.status.includes('Invitation') || player.status ==='Error sending invitation'" >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                            </svg>
                            &nbsp;Copy Link
                        </SecondaryButton>
                    </div>
                </td>
                <td class="px-4 py-2 text-center" v-if="$page.props.auth.user.id === $page.props.host.id && player.id === $page.props.host.id" >
                    <div>
                        <SecondaryButton v-if="player.status.includes('Invitation') || player.status ==='Error sending invitation'" >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                            &nbsp;Take Quiz
                        </SecondaryButton>
                        <SecondaryButton v-if="player.status === 'Questions Answered'" >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                            &nbsp;Edit Quiz
                        </SecondaryButton>
                    </div>
                </td>
                <td class="px-4 py-2 text-center" v-if="$page.props.auth.user.id === $page.props.host.id" >
                    <div v-if="$page.props.auth.user.id != player.id">
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
