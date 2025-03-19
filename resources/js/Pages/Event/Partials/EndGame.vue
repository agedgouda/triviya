<script setup>
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    game: Object,
});


const restartGame = () => {
    router.visit(route('games.startGame', { game: props.game.id, reset: -1 }));
};

</script>



<template>

    <div class="grid grid-cols-2 gap-4">
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
        <div>
            <PrimaryButton @click="restartGame()" >
                Replay with New Questions
            </PrimaryButton>
        </div>
    </div>
</template>
