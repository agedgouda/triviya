<script setup>
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BubblesLayout from "@/Layouts/BubblesLayout.vue";
import BubblesContainer from "@/Components/BubblesContainer.vue";
import GameBubble from "@/Components/GameBubble.vue";

const props = defineProps({
    game: Object,
    flash: Object,
});


const restartGame = () => {
    const clientTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    router.post(route('games.duplicate', { game: props.game.id }), {
        timeZone: clientTimeZone
    }, {
        onSuccess: (page) => {
            console.log(page)
            if (page.props.flash.success) {
                alert(page.props.flash.success); // Show success message
            }
        },
        onError: (errors) => {
            if (errors.error) {
                alert(errors.error); // Show error message
            }
        }
    });
};

const startBonusRound = () => {
    router.visit(route('games.startGame', { game: props.game.id, reset: -1 }));
};

</script>

<template>
    <BubblesLayout>
        <template #bubbles>
        <BubblesContainer>
            <GameBubble color="white">
                <div class="mb-5">
                    <div class="font-bold mt-2">🏆 And the Winner is</div>
                    Ask players to tally their scores and crown a champion!
                </div>
            </GameBubble>
            <GameBubble>
                <div class="border border-triviya-red sm:rounded-lg p-4  mb-5"  v-if="!game.status.includes('done') ">
                    <div class="text-xl font-bold ">Keep the Fun Going!</div>
                    <div class="font-bold mt-2">➕ Bonus Round</div>
                    Get 10 extra questions pulled from everyone’s earlier answers.

                    <div class="mt-2">
                        <PrimaryButton @click="startBonusRound()">
                            Play Bonus Round
                        </PrimaryButton>
                    </div>
                </div>
                <div class="border border-triviya-red sm:rounded-lg p-4">
                    <div class="font-bold">🔁 New Game</div>
                    <div>Start fresh! Players will get 10 new questions to answer before the next round.</div>
                    <div class="font-bold mt-2">
                        <PrimaryButton @click="restartGame()">
                            Start New Game
                        </PrimaryButton>
                    </div>
                </div>
            </GameBubble>
        </BubblesContainer>
    </template>
    </BubblesLayout>

</template>
