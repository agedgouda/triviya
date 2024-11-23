<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import GamesList from './Partials/GamesList.vue';
import GameDetails from './Partials/GameDetails.vue';

const props = defineProps({
    games: Object,
    game: Object,
    routeName: String,
});

</script>

<template>
    <AppLayout title="Games">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Games
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <template v-if="routeName === 'games'">
                        <GamesList :games="games" />
                    </template>
                    <template v-if="routeName === 'games.show'">
                        <GameDetails :game="game" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
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