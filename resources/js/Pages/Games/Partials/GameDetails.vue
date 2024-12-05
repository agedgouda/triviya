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



</script>

<template>
    <div class="m-10">

       <div class="mb-2">
            <div class="font-bold">{{ game.name }}</div>
            <div><span class="font-bold">Mode: </span>{{ game.mode.name }}</div>
            <div><span class="font-bold">Location: </span>{{ game.location }}</div>
            <div>{{ formatDate(game.date_time) }}</div>
            <div v-if="$page.props.auth.user.id === $page.props.host.id" class="flex items-center justify-end mt-4">
                <PrimaryButton class="ml-10 mb-5"  @click="goToEditPage" >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>

                    &nbsp;Edit
                </PrimaryButton>
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
                                    <PrimaryButton
                                        @click="handlePlayerAction(player, 'updateAttendance', 'put', { attendance: 1 })"
                                        class="mr-5"
                                        >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                                        </svg>
                                    </PrimaryButton>
                                    <DangerButton
                                        @click="handlePlayerAction(player, 'updateAttendance', 'put', { attendance: 0 })"
                                        >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                                        </svg>
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
