<script setup>
import { formatDate } from '@/utils';
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    game: Object,
    players: Array,
});

const goToEditPage = () => {
    router.visit(route('games.edit', props.game.id)); 
};

</script>

<template>
    <div class="m-10">

        <div class="mb-2">
            <div class="font-bold">{{ game.name }}</div>
            <div>{{ game.mode.name }}</div>
            <div>{{ formatDate(game.date_time) }}</div>
            <div class="flex items-center justify-end mt-4">
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