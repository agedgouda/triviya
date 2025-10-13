<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed,onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { formatDate } from '@/utils';
import { useFlash } from '@/Composables/useFlash';


const props = defineProps({
  canPurchase: Boolean,
  gameOptions: Array
});

const page = usePage();
const user = page.props.auth.user;

function purchase(quantity) {
  Inertia.post(route('games.purchase.store'), { quantity });
}
</script>


<template>
  <div class="p-6 max-w-md mx-auto">
    <h1 class="text-xl font-bold mb-4">Purchase Games</h1>
{{user}}
    <div v-if="!canPurchase" class="text-red-500">
      <p>Purchasing is currently unavailable.</p>
    </div>

    <div v-else>
      <p>Select the number of games to purchase:</p>
      <div class="mt-4 space-x-2">
        <button
          v-for="qty in gameOptions"
          :key="qty"
          @click="purchase(qty)"
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          {{ qty }} Game{{ qty > 1 ? 's' : '' }}
        </button>
      </div>
    </div>
  </div>
</template>

