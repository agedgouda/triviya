<script setup>
import { formatDate } from '@/utils';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Table from '@/Components/Table.vue';

const props = defineProps({
    answers: Object,
});

</script>
<template>
    <div v-for="answer in answers">
        {{ answer.question_number }}.  {{ answer.question_text }}<br>
        {{ answer.answer }}<br>
        <strong>{{ answer.player_name }}</strong>
        <hr class="h-px my-4 bg-gray-500 border-0">
    </div>
</template>

