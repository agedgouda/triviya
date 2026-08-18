<script setup>
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BubblesLayout from "@/Layouts/BubblesLayout.vue";
import BubblesContainer from "@/Components/BubblesContainer.vue";
import GameBubble from "@/Components/GameBubble.vue";
import { useFlash } from '@/Composables/useFlash';
import { usePage } from '@inertiajs/vue3';

const { setFlash } = useFlash();
const page = usePage();

const props = defineProps({
    game: Object,
    flash: Object,
    hasBonusQuestionsAvailable: Boolean,
    bonusLockedForPurchase: Boolean,
});


const restartGame = () => {
    const clientTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    router.post(route('games.duplicate', { game: props.game.id }), {
        timeZone: clientTimeZone
    }, {
        onSuccess: (page) => {
            if (page.props.flash.success) {
                setFlash(page.props.flash.success);
            }
        },
        onError: (errors) => {
            if (errors.error) {
                setFlash(errors.error);
            }
        }
    });
};

const startBonusRound = () => {
    router.visit(route('games.startGame', { game: props.game.id, reset: -1 }));
};

const goToPurchase = () => {
    router.visit(route('games.purchase'));
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
            <GameBubble v-if="game.status.includes('bonus') && hasBonusQuestionsAvailable">
                <div class="border border-triviya-red sm:rounded-lg p-4  mb-5">
                    <div class="text-xl font-bold ">Keep the Fun Going!</div>
                    <div class="font-bold mt-2">➕ Bonus Round</div>
                    <template v-if="bonusLockedForPurchase">
                        Buy games to unlock your bonus round.
                        <div class="mt-2">
                            <PrimaryButton @click="goToPurchase()">
                                Buy Games
                            </PrimaryButton>
                        </div>
                    </template>
                    <template v-else>
                        Get 10 extra questions pulled from everyone’s earlier answers.
                        <div class="mt-2">
                            <PrimaryButton @click="startBonusRound()">
                                Play Bonus Round
                            </PrimaryButton>
                        </div>
                    </template>
                </div>
            </GameBubble>
            <GameBubble color="white">
                <span class="font-bold">Games available:</span> {{ page.props.auth.user.games_available }}
                <PrimaryButton v-if="page.props.auth.user.games_available === 0" @click="goToPurchase()" class="ml-2">
                    Buy More Games
                </PrimaryButton>
            </GameBubble>
        </BubblesContainer>
    </template>
    </BubblesLayout>

</template>
