<script setup>

import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';


const props = defineProps({
    games: Object,
});

const { data: gamesList, current_page, last_page, links } = props.games;
const showDone = ref(false);

const goToGame = (gameId) => {
  window.location.href = route('games.show', gameId); // Redirect to /games/{gameId}
};

const filteredGames = computed(() => {
    return gamesList.filter(game => {
        const hasDone = game.status.includes('done');
        return showDone.value ? hasDone : !hasDone;
    })
});

const gameStatus = (status) => {
    if (status.includes('done')) {
        status = 'played'
    }
    return status.replace(/\b\w/g, c => c.toUpperCase())
}

const fetchPage = (url) => {
    if (url) {
        router.visit(url);
    }
};

</script>

<template>
    <div class="mb-4 flex justify-end">
        <SecondaryButton
            @click="showDone = !showDone"
        >
            {{ showDone ? 'Show Active Games' : 'Show Completed Games' }}
        </SecondaryButton>
    </div>

    <Table class="min-w-full table-auto " :has-hover="true" :has-pointer="true">
        <template #header>
                <th class="px-4 py-2 text-left sm:px-4 py-1 ">Your Games</th>
                <th class="hidden sm:table-cell px-2 py-1 text-center"># Playing</th>
                <th class="px-4 py-2 text-center sm:px-4 py-1 ">Quiz Status</th>
                <th class="px-4 py-2 text-center sm:px-4 py-1 ">Game Status</th>
        </template>

            <template #default="{ rowClass }">
            <tr v-for="game in filteredGames"
                :key="game.id"
                :class="[
                    rowClass
                ]"
                @click="goToGame(game.id)"
            >
                <td class="px-2 sm:px-4 py-1 sm:py-2">{{ game.name }}</td>
                <td class="hidden sm:table-cell px-2 py-1 text-center">{{ game.players_count }}</td>
                <td class="px-2 sm:px-4 py-1 sm:py-2 text-center">{{ game.current_user_status }}</td>
                <td class="px-2 sm:px-4 py-1 sm:py-2 text-center">{{ gameStatus(game.status) }}</td>

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
                        link.hover ? 'bg-triviya-red text-white' : 'bg-white text-triviya-red',
                        link.active ? 'bg-triviya-red text-white' : 'bg-white text-triviya-red',
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
