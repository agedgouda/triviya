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
        <BubblesContainer>
            <GameBubble color="white">
                <div class="mb-5">
                    <div class="text-xl font-bold">That’s a Wrap!</div>
                    Well played, {{game.host[0].first_name}}. The game is over. Let’s find out who won.
                    <div class="font-bold mt-2">🏆 And the Winner is</div>
                    Ask players to tally their scores and crown a champion!
                    <div class="font-bold mt-2">🎤 Say a Few Words</div>
                    Give your crew a big congrats — they were the trivia all along.
                </div>
            </GameBubble>
            <GameBubble>
                <div class="text-xl font-bold ">Keep the Fun Going!</div>
                <div  v-if="game.status !== 'done-bonus' ">
                    <div>
                        <div class="font-bold mt-2">➕ Bonus Round</div>
                        Get 10 extra questions pulled from everyone’s earlier answers.
                    </div>
                    <div>
                        <PrimaryButton @click="startBonusRound()">
                            Play Bonus Round
                        </PrimaryButton>
                    </div>
                </div>
                <div class="font-bold mt-5">🔁 New Game</div>
                <div>Start fresh! Players will get 10 new questions to answer before the next round.</div>
                <div class="font-bold mt-2">
                    <PrimaryButton @click="restartGame()">
                        Start New Game
                    </PrimaryButton>
                </div>
            </GameBubble>
        </BubblesContainer>
    </BubblesLayout>

</template>
