<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Table from '@/Components/Table.vue';
import Modal from '@/Components/Modal.vue';
import { useFlash } from '@/Composables/useFlash';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import StartGameIcon from '@/Components/Icons/StartGameIcon.vue';
import RemovePlayerIcon from '@/Components/Icons/RemovePlayerIcon.vue';

const props = defineProps({
  game: Object,
  players: Array,
  inviteMessage: String,
});

const { setFlash } = useFlash();
const currentDomain = window.location.origin;
const page = usePage();
const host = page.props.host;
const game = ref({ ...props.game });

// Computed helpers
const questionsAnsweredCount = computed(() =>
  props.players.filter(p => p.status === 'Questions Answered' || p.status === 'Quiz Complete').length
);

const playersRemainingToAnswer = computed(() => props.players.length - questionsAnsweredCount.value);

const isHost = computed(() => page.props.auth.user.id === host.id);

const showRemoveModal = ref(false);
const playerToRemove = ref(null);

// Helper functions
const goToEditPage = () => router.visit(route('games.edit', props.game.id));
const startGame = () => router.visit(route('games.startGame', { game: props.game.id }));


const quizButtonText = (player) => {
  if (player.status === 'Quiz Available') return 'Start Quiz';
  if (player.status !== 'Quiz Complete') return 'Finish Quiz';
  return 'Review Quiz';
};

const copyText = async (text, successMessage) => {
  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(text);
    } else {
      const textarea = document.createElement('textarea');
      textarea.value = text;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
    }
    setFlash(successMessage);
  } catch {
    setFlash('Failed to copy.');
  }
};

const copyInvite = () => {
  // Combine message + game URL
  const textToCopy = `${props.inviteMessage}\n\n${currentDomain}/questions/${props.game.id}`;
  copyText(textToCopy, 'Copied invite link to clipboard!');
};;

const promptRemovePlayer = (player) => {
    playerToRemove.value = player;
    showRemoveModal.value = true;
};


const confirmRemovePlayer = async () => {
    if (!playerToRemove.value) return;

    try {
        const response = await axios.delete(route('games.removePlayer', { game: props.game.id, user: playerToRemove.value.id }));
        if (response.data.status === 'success') {
            const index = props.players.findIndex(p => p.id === playerToRemove.value.id);
            game.value = response.data.game;
            if (index !== -1) props.players.splice(index, 1);
            setFlash('Player removed successfully!');
        } else {
            setFlash(`Error: ${response.data.message}`);
        }
    } catch (error) {
        console.error(error);
        setFlash('Failed to remove player.');
    } finally {
        showRemoveModal.value = false;
        playerToRemove.value = null;
    }
};

</script>

<template>

<Modal
    :show="showRemoveModal"
    title="Remove Player?"
    @close="showRemoveModal = false"
>
    <p>Are you sure you want to remove this player from the game?</p>
    <div class="mt-4 flex justify-end gap-2">
    <PrimaryButton @click="confirmRemovePlayer(player)">
        Yes, remove
    </PrimaryButton>
    <SecondaryButton @click="showRemoveModal = false">
        Cancel
    </SecondaryButton>
    </div>
</Modal>


  <!-- Game Header -->
  <div class="flex justify-between items-start gap-4">
    <div class="flex gap-4">
      <div class="flex flex-col justify-center">
        <div class="flex items-center sm:text-sm md:text-2xl text-triviyaRegular font-bold">
            {{ game.name }}
            <PrimaryButton
            v-if="isHost"
            @click="goToEditPage"
            class="ml-2 p-1"
            >
                <EditIcon class="h-3 w-3" />
            </PrimaryButton>
        </div>
        <div class="sm:text-xs md:text-lg font-bold text-triviyaRegular">Location: {{ game.location }}</div>
      </div>
    </div>

    <div class="flex justify-end" v-if="(game.status === 'start' || game.status === 'in progress' || game.status === 'ready') && isHost">
      <PrimaryButton @click="startGame">
            <StartGameIcon class="hidden md:inline-block h-4 w-4" />
        &nbsp;<span>{{ game.status === 'in progress' ? 'Continue Playing' : 'Start Game' }}</span>
      </PrimaryButton>
    </div>

    <div class="flex-1 flex justify-center mt-2 text-4xl font-bold" v-if="game.status.includes('done')">GAME COMPLETE</div>
  </div>

  <!-- Host Instructions -->
  <div v-if="isHost" class="mt-4">
    <div v-if="['new', 'ready'].includes(game.status)">
      Congrats, you’re the host of TriviYa. You’ll need to:
      <ul class="list-disc pl-4 ml-0">
        <li v-if="players.length < 4"><span class="font-bold">Invite</span> at least {{ 4 - players.length }} more players to join.</li>
        <li><span class="font-bold">Share</span> your unique game invite link via email, text, or group chat — whatever works best for you.</li>
        <li><span class="font-bold">Watch</span> as players’ names and status appear below as they register.</li>
        <li  v-if=" players.length > 1"><span class="font-bold">Remember:</span> TriviYa only works with 4–12 players. {{ players.length }} players have joined.</li>
        <li  v-if="game.status === 'new'"><span class="font-bold">START GAME</span> button appears below when all answers are in.</li>
      </ul>
    </div>

    <div v-if="['new', 'ready'].includes(game.status) && !game.is_full" class="mt-3 flex flex-col gap-2">
        <div class="mt-3 flex flex-col gap-2">
            <div class="flex justify-start">
                <SecondaryButton @click="copyInvite(game)">
                &nbsp;Copy Link
                </SecondaryButton>
            </div>
            <div class="font-bold text-triviyaRegular italic">
                TriviYa doesn’t send invites – you control who gets your link.
            </div>
        </div>
    </div>
  </div>

  <!-- Players Table -->
  <div class="mt-5">
    <span class="text-red-800">{{ $page.props.errors.msg }}</span>
    <Table class="min-w-full table-auto">
      <template #header>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-center">Questions</th>
        <th class="px-4 py-2 text-center"></th>
      </template>

      <template #default="{ rowClass }">
        <tr v-for="player in players" :key="player.id" :class="[rowClass]">
          <td class="px-4 py-2 flex items-center space-x-2">
            <img class="w-8 h-8 rounded-full object-cover border-triviyaRegular border-2" :src="player.profile_photo_url" alt="Player Photo" />
            <span>{{ player.name }}</span>
          </td>
          <td class="px-4 py-2 text-center">{{ player.status }}</td>
          <td class="px-4 py-2 text-center">
            <PrimaryButton
              v-if="$page.props.auth.user.id === player.id && ['new', 'ready'].includes(game.status)"
              :class="{ 'pulse-button': player.status === 'Quiz Available' }"
              @click="$inertia.visit(route('questions.showQuestions', { game: game.id, user: player.id }))"
            >
              {{ quizButtonText(player) }}
            </PrimaryButton>

            <div v-else-if="isHost && $page.props.auth.user.id !== player.id && ['new', 'ready'].includes(game.status)">
                <DangerButton @click="promptRemovePlayer(player)" class="ml-2">
                    <RemovePlayerIcon class="h-4" />
                </DangerButton>
            </div>
          </td>
        </tr>
      </template>
    </Table>
  </div>
</template>

<style scoped>
@keyframes pulse-scale {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.pulse-button {
  animation: pulse-scale 1s ease-in-out infinite;
}
</style>
