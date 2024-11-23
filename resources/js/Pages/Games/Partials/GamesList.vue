<script setup>

import { formatDate } from '@/utils';
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';


const props = defineProps({
    games: Object,
});



const goToGame = (gameId) => {
  window.location.href = route('games.show', gameId); // Redirect to /games/{gameId}
};
</script>

<template>

    <table class="min-w-full table-auto">
        <thead>
            <tr class="border">
            <th class="px-4 py-2 text-left">Game Name</th>
            <th class="px-4 py-2 text-center">Date and Time</th>
            <th class="px-4 py-2 text-center">Players Count</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="game in games" 
                :key="game.id" 
                class="border-b cursor-pointer hover:bg-teal-700 hover:text-white"
                @click="goToGame(game.id)"
            >
            <td class="px-4 py-2">{{ game.name }}</td>
            <td class="px-4 py-2 text-center">{{ formatDate(game.date_time) }}</td>
            <td class="px-4 py-2 text-center">{{ game.players_count }}</td>
            </tr>
        </tbody>
    </table>
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