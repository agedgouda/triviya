<script setup>
import { formatDate } from '@/utils';
import { ref } from 'vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    game: Object,
    players: Array,
});

</script>

<template>
    <div class="m-10">

        <div class="mb-2">
            <div class="font-bold">{{ game.name }}</div>
            <div>{{ game.mode.name }}</div>
            <div>{{ formatDate(game.date_time) }}</div>
            <div class="mt-5">
                <table class="min-w-full table-auto ">
                    <thead>
                        <tr class="border bg-teal-700 text-white">
                            <th class="px-4 py-2 text-left">Player Name</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="player in players" 
                            :key="player.id" 
                            class="border cursor-pointer hover:bg-teal-700 hover:text-white"
                        >
                        <td class="px-4 py-2">{{ player.first_name }} {{ player.last_name }}</td>
                        <td class="px-4 py-2 text-center">{{ player.status }}</td>
                        <td class="px-4 py-2 text-center"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>