<script setup>

import { formatDate } from '@/utils';
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';


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
  <div class="mx-5">
      <table class="min-w-full table-auto">
          <thead class="bg-amber-100">
              <tr class="border">
                  <th class="px-4 py-2 text-left ">Game Name</th>
                  <th class="px-4 py-2 text-center">Date and Time</th>
                  <th class="px-4 py-2 text-center"># Invited</th>
                  <th class="px-4 py-2 text-center"># Playing</th>
                  <th class="px-4 py-2 text-center"># Not Playing</th>
              </tr>
          </thead>
          <tbody>
              <tr v-for="game in gamesList" 
                  :key="game.id" 
                  class="border-b cursor-pointer hover:bg-teal-700 hover:text-white"
                  @click="goToGame(game.id)"
              >
                  <td class="px-4 py-2">{{ game.name }}</td>
                  <td class="px-4 py-2 text-center">{{ formatDate(game.date_time) }}</td>
                  <td class="px-4 py-2 text-center">{{ game.total }}</td>
                  <td class="px-4 py-2 text-center">{{ game.attending }}</td>
                  <td class="px-4 py-2 text-center">{{ game.not_attending }}</td>
              </tr>
          </tbody>
      </table>

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