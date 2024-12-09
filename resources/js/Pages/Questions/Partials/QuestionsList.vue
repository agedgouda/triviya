<script setup>
import { formatDate } from '@/utils';
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import axios from 'axios';
import Table from '@/Components/Table.vue';


const props = defineProps({
    questions: Object,
});

const selectedModes = ref([1, 2, 3]);
const editRow = ref();
let oldQuestion = null;

const editQuestion = (index, question) => {
    editRow.value = index;
    oldQuestion = JSON.parse(JSON.stringify(question));
}

const saveEdit = async (question) => {
    console.log(`Saving changes for row ${question.id}`);

    try {
        // Send the entire question object in the PUT request
        const response = await axios.put(route('questions.update', question.id), question);

        console.log('Response:', response.data);
        console.log(`Changes saved for row ${question.id}`);
    } catch (error) {
        console.error(`Failed to save changes for row ${question.id}:`, error.response?.data || error.message);
    }
    editRow.value = null; // Exit edit mode
};

const cancelEdit = (index) => {
    editRow.value = null;
    filteredQuestions.value[index] = JSON.parse(JSON.stringify(oldQuestion));
}


const filteredQuestions = computed(() => {
  return props.questions.filter(question => {
    // Filter questions where at least one mode matches selected values
    const matchingModes = question.modes.filter(mode => selectedModes.value.includes(mode.id));
    return matchingModes.length > 0;  // Only include questions with matching modes
  });
});

function isModeSelected(modeId, question) {
    return question.modes.some(mode => mode.id === modeId);
}

function toggleMode(mode, question) {
    const index = question.modes.findIndex(selectedMode => selectedMode.id === mode.id);
    if (index !== -1) {
        // If mode is already in the array, remove it
        question.modes.splice(index, 1);
    } else {
        // If mode is not in the array, add it
        question.modes.push(mode);

    }
}

</script>

<template>

  <div class="mx-5">
    <div  class="flex items-center gap-4 mb-3 w-full">
        <div v-for="mode in $page.props.modes" :key="mode.id">
            <input type="checkbox" :value="mode.id" v-model="selectedModes" />
            <span>{{ mode.name }}</span>
        </div>
    </div>

    <Table>
        <template #header>
            <th class="text-left">Question</th>
            <th class="text-left">Type</th>
            <th class="text-center">Mode</th>
            <th class="text-left"></th>
        </template>
        <!-- Use v-slot to access the slot props -->
        <template #default="{ rowClass }">
            <tr
                v-for="(question, index) in filteredQuestions"
                :key="question.id"
                :class="rowClass"
            >
                <!-- Check if the current row is being edited -->
                <template v-if="index === editRow">
                    <!-- Editable row -->
                    <td>
                        <!-- Render editable inputs or components here -->
                        <input
                            v-model="question.question_text"
                            type="text"
                            class="border p-2 w-full text-black"
                            placeholder="Edit Question Text"
                        />
                    </td>
                    <td>
                        <select
                            v-model="question.question_type"
                            class="border p-2 w-full text-black"
                        >
                            <option value="text">Text</option>
                            <option value="date">Date</option>
                        </select>
                    </td>
                    <td>
                        <div v-for="mode in $page.props.modes" :key="mode.id" class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                :value="mode.id"
                                :checked="isModeSelected(mode.id, question)"
                                @change="toggleMode(mode, question)"
                            />
                            <span>{{ mode.name }}</span>
                        </div>
                    </td>
                    <td class="text-right">
                        <!-- Add a save button or other interactive elements -->
                        <PrimaryButton @click="saveEdit(question)" class="mb-2 text-center">
                            Save
                        </PrimaryButton>
                        <DangerButton type="button" @click="cancelEdit(index)" class="ml-2 text-center">
                            Cancel
                        </DangerButton>
                    </td>
                </template>
                <template v-else>
                    <!-- Normal row -->
                    <td>{{ question.question_text }}</td>
                    <td>{{ question.question_type.charAt(0).toUpperCase() + question.question_type.slice(1) }}</td>

                    <td >
                        <ul>
                            <li v-for="mode in question.modes" :key="mode.id" class="text-center">{{ mode.name }}</li>
                        </ul>
                    </td>
                    <td class="text-center">
                        <SecondaryButton type="button" @click="editQuestion(index,question)">
                            Edit
                        </SecondaryButton>
                    </td>
                </template>
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
