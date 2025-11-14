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
//import EditIcon from '@/Components/Icons/EditIcon.vue';
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
const invitationLink = `${page.props.short_url}/q/${props.game.short_url}`
const textToCopy = `${props.inviteMessage}\n\n${invitationLink}`;

const showRemoveModal = ref(false);
const showManualCopy = ref(false);
const playerToRemove = ref(null);

const startGame = () => router.visit(route('games.startGame', { game: props.game.id }));


const quizButtonText = (player) => {
  if (player.status === 'Available') return 'Take Quiz';
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
    <div class="flex flex-col md:flex-row justify-between items-start mb-2 md:mb-1 space-y-2 md:space-y-0 md:space-x-4">
    <!-- Left Column: Game Info -->
    <div class="flex flex-col justify-center space-y-1 flex-1 min-w-0">
        <!-- Game Name -->
        <div class="font-bold text-lg sm:text-sm md:text-2xl text-triviyaRegular break-words">
        {{ game.name }}
        </div>

        <!-- Location -->
        <div class="text-triviyaRegular break-words">
        <span class="font-bold">Location:</span>
        <span class="text-black">{{ game.location }}</span>
        </div>

        <!-- Quiz Link -->
        <div class="break-words">
        <span class="text-black">
            <span class="font-bold text-triviyaRegular">Quiz Link:</span>
            {{ invitationLink }}
        </span>

        <!-- Copy Button (desktop only) -->
        <div class="hidden md:inline-block text-sm ml-2" v-if="['new', 'ready','sequel'].includes(game.status)">
            <PrimaryButton @click="copyInvite(game)" class="w-sm">
            Copy Link
            </PrimaryButton>
        </div>
        </div>
    </div>

    <!-- Right Column: Start/Continue Button -->
    <div class="flex justify-end mt-2 md:mt-0 flex-shrink-0" v-if="isHost && ['in progress', 'ready'].includes(game.status)">
        <PrimaryButton @click="startGame">
        <StartGameIcon class="hidden md:inline-block h-4 w-4" />
        &nbsp;<span>{{ game.status === 'in progress' ? 'Continue Playing' : 'Start Game' }}</span>
        </PrimaryButton>
    </div>

    <!-- Game Complete Indicator -->
    <div class="flex justify-center mt-2 md:mt-0 text-xl sm:text-lg md:text-4xl font-bold flex-1" v-if="game.status.includes('done')">
        GAME COMPLETE
    </div>
    </div>


  <!-- Host Instructions -->
  <div v-if="isHost" >
    <div v-if="['new', 'ready','sequel'].includes(game.status)">
        <div >

            <div class="font-bold text-triviyaRegular">👥  Step 1 – Invite players</div>
            <div class="inline-block mb-2" v-if="players.length <4 ">You’ll need at least {{ 4 - players.length }} more (up to 12) to start the game.</div>
            <div class="inline-block mb-2" v-if="players.length >4 &&  players.length < 12 ">You can invite {{ 12 - players.length }} more to start the game.</div>
            <div class="inline-block mb-2" v-if="players.length === 12 ">The game is full, start the game when everybody has taken the quiz!</div>
            <div class="font-bold text-triviyaRegular">🔗 Step 2 – Share the quiz link</div>
            <span class="hidden md:block">Tap Copy Link above, then paste and send it to the players in a text, email, or group chat.</span>
            <span class="md:hidden">Press and hold the link above, then tap Copy.
                Paste it into a text, email, or group chat then send it to the players.
            </span>

            <div class="font-bold mt-2 text-triviyaRegular">🤔 Step 3 – Take your quiz</div>
            You’re a player too! Tap Take Quiz below to answer 10 quick questions about yourself.
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
