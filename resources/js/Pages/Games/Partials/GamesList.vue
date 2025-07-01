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
    <div class="grid grid-cols-3 text-triviya-darkPurple w-full max-w-3xl mx-auto">
    <!-- Header -->
    <div class="px-4 py-2 font-bold text-left">Your Games</div>
    <div class="px-4 py-2 font-bold text-center"># Playing</div>
    <div class="px-4 py-2 font-bold text-right">Status</div>

    <!-- Rows -->
    <template v-for="game in gamesList" :key="game.id">
    <div
        class="px-4 pt-2 col-span-3 cursor-pointer transition-all"
        @click="goToGame(game.id)"
    >
        <div class="grid grid-cols-3 border-b-2 border-transparent hover:border-triviya-darkPurple leading-none mb-4">
        <div class="text-left">{{ game.name }}</div>
        <div class="text-center">{{ game.players_count }}</div>
        <div class="text-right">{{ game.status }}</div>
        </div>
    </div>
    </template>

    </div>



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
