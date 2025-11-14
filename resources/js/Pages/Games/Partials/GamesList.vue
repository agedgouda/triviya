<script setup>

import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';


const props = defineProps({
    games: Object,
});

const gamesList = props.games;
const showDone = ref(false);

// Pagination state
const currentPage = ref(1);
const perPage = 10;

const goToGame = (gameId) => {
  window.location.href = route('games.show', gameId); // Redirect to /games/{gameId}
};

const filteredGames = computed(() => {
    return gamesList.filter(game => {
        const hasDone = game.status.includes('done');
        return showDone.value ? hasDone : !hasDone;
    })
});

// Slice for current page
const paginatedGames = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;
    return filteredGames.value.slice(start, end);
});

// Total pages for filtered array
const totalPages = computed(() => Math.ceil(filteredGames.value.length / perPage));

const gameStatus = (status) => {
    if (status.includes('done')) {
        status = 'played'
    }
    return status.replace(/\b\w/g, c => c.toUpperCase())
}

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) currentPage.value = page;
};

</script>

<template>
        <div v-if="props.games.length === 0" class="text-center">
            <div class="mb-1 font-bold">Let’s Do This!</div>
            <div class="mb-2">Click new game to get started</div>
        </div>

        <div class="mb-3">
            <PrimaryButton @click="router.visit(route('games.create'))">Host A New Game</PrimaryButton>
        </div>

        <div class="sm:mb-2 md:hidden">Tap a game name to view details.</div>
        <Table class="min-w-full table-auto " :has-hover="true" :has-pointer="true" v-if="props.games.length > 0">
            <template #header>
                    <th class="px-4 py-2 text-left sm:px-4 py-1 ">My Games</th>
                    <th class="hidden sm:table-cell px-2 py-1 text-center">My Status</th>
                    <th class="px-4 py-2 text-center sm:px-4 py-1 ">Quiz Status</th>
                    <th class="px-4 py-2 text-center sm:px-4 py-1 ">Game Status</th>
            </template>

                <template #default="{ rowClass }">
                <tr v-for="game in paginatedGames"
                    :key="game.id"
                    :class="[
                        rowClass
                    ]"
                    @click="goToGame(game.id)"
                >
                    <td class="px-2 sm:px-4 py-1 sm:py-2">{{ game.name }}</td>
                    <td class="hidden sm:table-cell px-2 py-1 text-center">{{ game.is_host ? 'Host' : 'Player' }}</td>
                    <td class="px-2 sm:px-4 py-1 sm:py-2 text-center">{{ game.current_user_status }}</td>
                    <td class="px-2 sm:px-4 py-1 sm:py-2 text-center">{{ gameStatus(game.status) }}</td>

                </tr>
            </template>
        </Table>

        <div v-if="totalPages > 1" class="mt-4 flex justify-center space-x-1">
            <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded bg-white text-triviya-red hover:bg-triviya-red hover:text-white"
            >
            Prev
            </button>

            <button
            v-for="page in totalPages"
            :key="page"
            @click="goToPage(page)"
            :class="[
                'px-3 py-1 border rounded',
                page === currentPage
                ? 'bg-triviya-red text-white'
                : 'bg-white text-triviya-red hover:bg-triviya-red hover:text-white'
            ]"
            >
            {{ page }}
            </button>

            <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded bg-white text-triviya-red hover:bg-triviya-red hover:text-white"
            >
            Next
            </button>
        </div>

        <div class="mb-5">
            <SecondaryButton
                @click="showDone = !showDone"
                class="mt-3"
                v-if="gamesList.some(game => game.status.includes('done'))"
            >
                {{ showDone ? 'Show Active Games' : 'Show Completed Games' }}
            </SecondaryButton>
        </div>
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

</style>
