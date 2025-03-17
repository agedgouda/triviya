<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

import Play from '../Event/Partials/Play.vue';
import End from '../Event/Partials/End.vue';

import { formatDate } from '@/utils';

const props = defineProps({
    game: Object,
    questions: Object,
    answers: Object,
    round: Number,
    firstQuestion:Number,
    routeName: String,
    error: String
});

const flashMessage = ref(null);
const fadeOut = ref(false);

</script>

<template>
    <AppLayout title="Games">
        <template #header>
            <div v-if="routeName === 'games.showQuestions'">
                <div >{{game.name}}</div>
                <div class="text-base">Hosted by {{ game.host[0].first_name }} {{ game.host[0].last_name }}</div>
                <div class="text-base">{{ formatDate(game.date_time) }}</div>
                <div class="text-base">{{ game.location }}</div>
            </div>
        </template>

        <div>

            <div class="mx-5">
                <div
                    v-if="flashMessage"
                    :class="['transition-opacity duration-1000', { 'opacity-0': fadeOut }]"
                >
                    {{ flashMessage }}
                </div>
                <div
                    v-if="$page.props.flash.message"
                    :class="['transition-opacity duration-1000', { 'opacity-0': fadeOut }]"
                >
                    {{ $page.props.flash.message }}
                </div>
                <template v-if="routeName === 'games.startGame' || routeName === 'games.startRound'">
                    <Play :questions="questions" :round="round"    />
                </template>
                <template v-if="routeName === 'games.endRound'">
                    <End :answers="answers" :round="round"   />
                </template>
                <template v-if="routeName === 'games.endGame'">
                    <div class="video-container mb-5">
                        <iframe
                        width="560"
                        height="315"
                        src="https://www.youtube.com/embed/p_wfPij9lno"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                        </iframe>
                    </div>

                </template>
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
