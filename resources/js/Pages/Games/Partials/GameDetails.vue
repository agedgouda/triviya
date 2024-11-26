<script setup>
import { formatDate } from '@/utils';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    game: Object,
    players: Array,
});

const goToEditPage = () => {
    router.visit(route('games.edit', props.game.id)); 
};

const resendInvitation = async (player) => {
    try {
        // Make the POST request to resend the invitation
        const response = await axios.post(`/games/${props.game.id}/resend-invite/${player.id}`);

        // Handle success response
        if (response.data.status === 'success') {
            // Update the player's status in the UI
            player.status = response.data.message;
        } else {
            // Handle a failure response
            console.error(response.data.message);
        }
    } catch (error) {
        // Handle any errors from the API call
        console.error('Error resending invitation:', error);
    }
};

</script>

<template>
    <div class="m-10">

        <div class="mb-2">
            <div class="font-bold">{{ game.name }}</div>
            <div>{{ game.mode.name }}</div>
            <div>{{ formatDate(game.date_time) }}</div>
            <div v-if="$page.props.auth.user.id === game.host" class="flex items-center justify-end mt-4">
                <PrimaryButton class="ml-10 mb-5"  @click="goToEditPage" >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>

                    &nbsp;Edit
                </PrimaryButton>
            </div>


            <div class="mt-5">
                <table class="min-w-full table-auto ">
                    <thead>
                        <tr class="border bg-amber-100">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-center">Email</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="player in players" 
                            :key="player.id" 
                            class="border"
                        >
                            <td class="px-4 py-2">{{ player.first_name }} {{ player.last_name }}</td>
                            <td class="px-4 py-2 text-left">{{ player.email }}</td>
                            <td class="px-4 py-2 text-center">{{ player.status }}</td>
                            <td  class="px-4 py-2 text-center">
                                <SecondaryButton @click="resendInvitation(player)" v-if="$page.props.auth.user.id === $page.props.host.id" >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                    &nbsp;Resend Invitation
                                </SecondaryButton>
                                <div v-if="$page.props.auth.user.id === player.id" >Hey, it's you!</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>