<script setup>
import { formatDate } from '@/utils';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Table from '@/Components/Table.vue';
const props = defineProps({
    game: Object,
    players: Array,
});

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
        };

        // Ensure the action is valid
        if (!routes[action]) {
            throw new Error(`Invalid action: ${action}`);
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

const goToQuestions = (userId) => {
    router.visit(route('games.showQuestions', { game: props.game.id, user: userId }));
};

const goToAnswers = (userId) => {
    router.visit(route('games.showAnswers', { game: props.game.id }));
};



</script>

<template>
    <div class="m-10">

       <div class="mb-2">
            <div class="font-bold">{{ game.name }}</div>
            <div><span class="font-bold">Mode: </span>{{ game.mode.name }}</div>
            <div><span class="font-bold">Location: </span>{{ game.location }}</div>
            <div>{{ formatDate(game.date_time) }}</div>
            <div v-if="$page.props.auth.user.id === $page.props.host.id" class="flex items-center w-full mt-4">
                <div>
                    <SecondaryButton  @click="goToAnswers">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                        </svg>
                        View Answers
                    </SecondaryButton>
                </div>
                <div class="flex-grow"></div>
                <div class="justify-end">
                    <PrimaryButton class="ml-10"  @click="goToEditPage" >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>

                        &nbsp;Edit
                    </PrimaryButton>
                </div>
            </div>

            <div class="mt-5">
                <span class="text-red-800">{{ $page.props.errors.msg }}</span>
                <Table class="min-w-full table-auto ">
                    <template #header>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-center">Email</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-center"></th>
                    </template>
                    <template #default="{ rowClass }">
                    <tr v-for="player in players"
                        :key="player.id"
                        :class="rowClass"
                    >
                        <td class="px-4 py-2">{{ player.first_name }} {{ player.last_name }}</td>
                        <td class="px-4 py-2 text-left">{{ player.email }}</td>
                        <td class="px-4 py-2 text-center" v-if="!player.status.includes('Questions')">
                            {{ player.status }}
                        </td>
                        <td class="px-4 py-2 text-center" v-else >
                            <a href="#" @click.prevent="goToQuestions(player.id)">{{ player.status }}</a>
                        </td>
                        <td  class="px-4 py-2 text-center">
                            <div v-if="$page.props.auth.user.id === $page.props.host.id" >
                                <SecondaryButton @click="handlePlayerAction(player, 'resendInvite', 'post')" v-if="player.status.includes('Invitation')" >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                    &nbsp;Resend Invitation
                                </SecondaryButton>
                            </div>
                            <div v-if="$page.props.auth.user.id === player.id" >
                                <div v-if="player.status.includes('Invitation')">
                                    <div>Please RSVP</div>
                                    <PrimaryButton
                                        @click="handlePlayerAction(player, 'updateAttendance', 'put', { attendance: 1 })"
                                        class="mr-5"
                                        >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        I'll be there
                                    </PrimaryButton>
                                    <DangerButton
                                        @click="handlePlayerAction(player, 'updateAttendance', 'put', { attendance: 0 })"
                                        >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>

                                         I can't make it
                                    </DangerButton>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
                </Table>
            </div>
        </div>
    </div>
</template>
