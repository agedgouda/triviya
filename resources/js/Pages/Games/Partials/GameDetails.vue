<script setup>
import { ref, computed,onMounted } from 'vue';
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
  props.players.filter(p => p.status === 'Questions Answered' || p.status === 'Complete').length
);

const playersRemainingToAnswer = computed(() => props.players.length - questionsAnsweredCount.value);

const isHost = computed(() => page.props.auth.user.id === host.id);
const textToCopy = `${props.inviteMessage}\n\n${currentDomain}/questions/${props.game.id}`;
//const invitationLink = `${currentDomain}/questions/${props.game.id}`;
const invitationLink = `${currentDomain}/q/${props.game.short_url}`



const showRemoveModal = ref(false);
const showManualCopy = ref(false);
const playerToRemove = ref(null);

// Helper functions
const goToEditPage = () => router.visit(route('games.edit', props.game.id));
const startGame = () => router.visit(route('games.startGame', { game: props.game.id }));


const quizButtonText = (player) => {
  if (player.status === 'Available') return 'Start Quiz';
  if (player.status !== 'Completed') return 'Finish Quiz';
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
  //const textToCopy = `${props.inviteMessage}\n\n${currentDomain}/questions/${props.game.id}`;
  copyText(invitationLink, 'Copied invite link to clipboard!');
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
    :show="showManualCopy"
    title="Invite Player"
    @close="showRemoveModal = false"
>
  <div class="text-sm mb-2">Tap and hold to copy this text:</div>
  <textarea readonly class="w-full p-2 text-sm bg-gray-100">{{ textToCopy }}</textarea>
  <PrimaryButton @click="showManualCopy = false">Close</PrimaryButton>
</Modal>

<Modal
    :show="showRemoveModal"
    title="Remove Player?"
    @close="showRemoveModal = false"
>1
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

    <div class="flex justify-end" v-if="isHost && ['in progress', 'ready'].includes(game.status)">
      <PrimaryButton @click="startGame">
            <StartGameIcon class="hidden md:inline-block h-4 w-4" />
        &nbsp;<span>{{ game.status === 'in progress' ? 'Continue Playing' : 'Start Game' }}</span>
      </PrimaryButton>
    </div>

    <div class="flex-1 flex justify-center mt-2 md:text-4xl sm:text-sm font-bold" v-if="game.status.includes('done')">GAME COMPLETE</div>
  </div>

  <!-- Host Instructions -->
  <div v-if="isHost" class="mt-4">
    <div v-if="['new', 'ready'].includes(game.status)">
      <span class="hidden md:block">Congrats, you’re the host of TriviYa. You’ll need to:</span>
      <ul class="hidden md:inline-block list-disc pl-4 ml-0">
        <li v-if="players.length < 4"><span class="font-bold">Invite</span> at least {{ 4 - players.length }} more player<span v-if="4 - players.length > 1">s</span> to join.</li>
        <li><span class="font-bold">Share</span> your unique game invite link via email, text, or group chat — whatever works best for you.</li>
        <li><span class="font-bold">Watch</span> as players’ names and status appear below as they register.</li>
        <li  v-if=" players.length > 1"><span class="font-bold">Remember:</span> TriviYa only works with 4–12 players. {{ players.length }} players have joined.</li>
        <li  v-if="game.status === 'new'"><span class="font-bold">START GAME</span> button appears below when all answers are in.</li>
      </ul>

      <div class="md:hidden sm:inline-block text-sm">
        Congrats, you’re the host!
        <ul>
            <li v-if="players.length < 4">Share your game link with 3+ friends and watch their names appear below as they join.</li>
            <li v-if="game.status === 'new'">Start the game when everyone’s quiz is completed.</li>
        </ul>
      </div>
    </div>

    <div v-if="game.status === 'sequel'">
        <p>Congrats, you’ve started a new game {{ game.name }}!</p>
        <p>Your players can join using this new link, or they’ll find the game waiting on their dashboard.</p>
    </div>

    <div v-if="['new', 'ready', 'sequel'].includes(game.status) && !game.is_full" class="mt-3 flex flex-col gap-2">
        <div class="mt-3 flex flex-col gap-2">
            <div class="justify-start">
                Send this link to invite friends and family to play:<br/>
                <div class="mr-3">{{ invitationLink }}
                    <div class="md:hidden sm:inline-block text-sm">
                        Select the URL above then tap the "Copy," then tell your adult children you are an iOS Expert
                    </div>

                    <div class="hidden md:inline-block text-sm">
                    <PrimaryButton @click="copyInvite(game)" class="w-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                        </svg>
                    </PrimaryButton>

                    </div>
                </div>
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
        <th class="px-2 sm:px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-center">Quiz Status</th>
        <th class="px-4 py-2 text-center"></th>
      </template>

      <template #default="{ rowClass }">
        <tr v-for="player in players" :key="player.id" :class="[rowClass]">
          <td class="px-2 sm:px-4 py-2 md:flex md:items-center md:space-x-2">
            <img
                class="hidden md:inline-block w-8 h-8 rounded-full object-cover border-triviyaRegular border-2"
                :src="player.profile_photo_url"
                alt="Player Photo"
            />
            <span>{{ player.name }}</span>
          </td>
          <td class="px-1 sm:px-4 py-2 text-center">{{ player.status }}</td>
          <td class="px-1 sm:px-4 py-2 text-center">
            <PrimaryButton
              v-if="$page.props.auth.user.id === player.id && ['new', 'ready', 'sequel'].includes(game.status)"
              :class="{ 'pulse-button': player.status === 'Available' }"
              @click="$inertia.visit(route('questions.showQuestions', { game: game.id, user: player.id }))"
            >
              {{ quizButtonText(player) }}
            </PrimaryButton>

            <div v-else-if="isHost && $page.props.auth.user.id !== player.id && ['new', 'ready', 'sequel'].includes(game.status)">
                <DangerButton @click="promptRemovePlayer(player)" class="ml-2">
                    <RemovePlayerIcon class="h-4" />
                </DangerButton>
            </div>
          </td>
        </tr>
      </template>
    </Table>

                <div class="flex justify-end my-5">
                <PrimaryButton @click="router.visit(route('games'))">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    &nbsp;Back To All Games
                </PrimaryButton>
            </div>
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
