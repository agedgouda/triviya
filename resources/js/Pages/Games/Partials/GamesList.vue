<script setup>

import { router } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';


const props = defineProps({
    games: Object,
});

const { data: gamesList, current_page, last_page, links } = props.games;

const goToGame = (gameId) => {
  window.location.href = route('games.show', gameId); // Redirect to /games/{gameId}
};

const fetchPage = (url) => {
    if (url) {
        router.visit(url);
    }
};

</script>

<template>

    <Table class="min-w-full table-auto " :has-hover="true" :has-pointer="true">
        <template #header>
                <th class="px-4 py-2 text-left sm:px-4 py-1 ">Your Games</th>
                <th class="px-4 py-2 text-center sm:px-4 py-1 "># Playing</th>
                <th class="px-4 py-2 text-center sm:px-4 py-1 ">Your Status</th>
                <th class="px-4 py-2 text-center sm:px-4 py-1 ">Game Status</th>
        </template>

            <template #default="{ rowClass }">
            <tr v-for="game in gamesList"
                :key="game.id"
                :class="[
                    rowClass
                ]"
                @click="goToGame(game.id)"
            >
                <td class="px-2 sm:px-4 py-1 sm:py-2">{{ game.name }}</td>
                <td class="px-2 sm:px-4 py-1 sm:py-2 text-center">{{ game.players_count }}</td>
                <td class="px-2 sm:px-4 py-1 sm:py-2 text-center">{{ game.current_user_status }}</td>
                <td class="px-2 sm:px-4 py-1 sm:py-2 text-center">{{ game.status }}</td>

            </tr>
        </template>
    </Table>

    <div v-if="links.length > 3">
        <!-- Pagination Links -->
        <div class="mt-4 flex justify-center">
            <nav class="inline-flex rounded-md shadow">
                <button
                    v-for="link in links"
                    :key="link.url"
                    :disabled="!link.url"
                    @click="fetchPage(link.url)"
                    :class="[
                        'px-4 py-2 border text-sm font-medium',
                        link.active ? 'bg-teal-700 text-white' : 'bg-white text-teal-700',
                        !link.url ? 'cursor-not-allowed' : ''
                    ]"
                >
                    <span v-html="link.label"></span>
                </button>
            </nav>
        </div>
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
