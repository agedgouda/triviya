<script setup>

import { formatDate } from '@/utils';
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import Checkbox from '@/Components/Checkbox.vue';


const props = defineProps({
    questions: Object,
});

const selectedModes = ref([1, 2, 3]);

//const { data: questions, current_page, last_page, links } = props.questions;

const goToQuestion = (questionId) => {
  //window.location.href = route('questions.show', questionId); // Redirect to /questions/{questionId}
};

const fetchPage = (url) => {
    if (url) {
        router.visit(url);
    }
};

const filteredQuestions = computed(() => {
  return props.questions.filter(question => {
    // Filter questions where at least one mode matches selected values
    const matchingModes = question.modes.filter(mode => selectedModes.value.includes(mode.id));
    return matchingModes.length > 0;  // Only include questions with matching modes
  });
});


</script>

<template>

  <div class="mx-5">
    <div  class="flex items-center gap-4 mb-3 w-full">
        <div v-for="mode in $page.props.modes" :key="mode.id">
            <input type="checkbox" :value="mode.id" v-model="selectedModes" />
            <span>{{ mode.name }}</span>
        </div>
    </div>

    <Table :hasHover="true">
        <template #header>
            <th class="px-4 py-2 text-left">Question</th>
            <th class="px-4 py-2 text-left">Type</th>
            <th class="px-4 py-2 text-left">Mode</th>
        </template>
        <!-- Use v-slot to access the slot props -->
        <template #default="{ rowClass }">
            <tr
                v-for="question in filteredQuestions"
                :key="question.id"
                :class="rowClass"
                @click="goToquestion(question.id)"
            >
                <td class="px-4 py-2">{{ question.question_text }}</td>
                <td class="px-4 py-2">{{ question.question_type }}</td>
                <td class="px-4 py-2">
                    <ul class="list-disc">
                        <li v-for="mode in question.modes">{{ mode.name }}</li>
                    </ul>


                </td>
            </tr>
        </template>
    </Table>
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
