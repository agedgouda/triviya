<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';

const props = defineProps({
    questions: Object,
});

</script>

<template>
    <div class="m-10">
    <Table>
        <template #header>
            <th class="px-4 py-2 text-left">Question</th>
            <th class="py-2 text-center">Answer</th>
            <th class="py-2 text-center">Player</th>
        </template>
        <!-- Use v-slot to access the slot props -->
        <template #default="{ rowClass }">
            <tr
                v-for="question in questions"
                :class="[rowClass]"
            >
                <td class="px-4 py-2 text-left">{{ question.question_text }}</td>
                <td class="py-2 text-center">{{ question.answer }}</td>
                <td class="py-2 text-center">{{ question.player_name }}</td>
            </tr>
        </template>
    </Table>
</div>
</template>
