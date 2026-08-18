<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  products: Array,
});

function purchase(product) {
  router.post(route('games.purchase.store'), { game_product_id: product.id });
}
</script>

<template>
  <AppLayout title="Buy Games">
    <div class="p-6 max-w-md mx-auto">
      <h1 class="text-xl font-bold mb-4 text-triviyaRegular">Buy Games</h1>

      <div v-if="products.length === 0" class="text-triviyaRegular">
        No game packs are available to purchase right now.
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="product in products"
          :key="product.id"
          class="flex items-center justify-between border border-triviya-red rounded-lg p-4"
        >
          <div>
            <div class="font-bold text-triviyaRegular">{{ product.name }}</div>
            <div class="text-sm text-black">
              {{ product.game_count }} game{{ product.game_count > 1 ? 's' : '' }} &mdash; ${{ product.price }}
            </div>
          </div>
          <PrimaryButton type="button" @click="purchase(product)">
            Buy
          </PrimaryButton>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
