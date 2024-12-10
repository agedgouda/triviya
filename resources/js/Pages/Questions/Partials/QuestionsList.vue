<script setup>
import { capitalizeFirstLetter } from '@/utils';
import { router } from '@inertiajs/vue3';
import { ref, computed,  } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';
import Table from '@/Components/Table.vue';
import AddQuestion from './AddQuestion.vue';


const props = defineProps({
    questions: Object,
});

const selectedModes = ref([1, 2, 3]);
const editRow = ref();
let oldQuestion = null;
const addQuestion = ref(false)

const editQuestion = (index, question) => {
    editRow.value = index;
    oldQuestion = JSON.parse(JSON.stringify(question));
}

const saveEdit = async (question) => {
    try {
        const response = await axios.put(route('questions.update', question.id), question);
        console.log(response.data.message);
    } catch (error) {
        console.error(`Failed to save changes for row ${question.id}:`, error.response?.data || error.message);
    }
    editRow.value = null; // Exit edit mode
};

const cancelEdit = (index) => {
    editRow.value = null;
    filteredQuestions.value[index] = JSON.parse(JSON.stringify(oldQuestion));
}

const closeAddQuestion = () => {
    addQuestion.value = false;
}
const { data: questionsList, current_page, last_page, links } = props.questions;

const filteredQuestions = computed(() => {
  // Access the questions from the paginated result
  const questions = props.questions.data;

  return questionsList.filter(question => {
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

const fetchPage = (url) => {
    if (url) {
        router.visit(url);
    }
};


</script>

<template>
    <div class="mx-5">

        <Modal :show=addQuestion><AddQuestion @close-add-question="closeAddQuestion"/></Modal>

        <div  class="flex items-center gap-4 mb-3 w-full">
            <div v-for="mode in $page.props.modes" :key="mode.id">
                <input type="checkbox" :value="mode.id" v-model="selectedModes" />
                <span>{{ mode.name }}</span>
            </div>

            <div class="">
            <PrimaryButton @click="addQuestion = true">
                Add Question
            </PrimaryButton>
            </div>

        </div>

        <Table>
            <template #header>
                <th class="text-left">Question</th>
                <th class="text-center w-32">Type</th>
                <th class="text-center w-96">Mode</th>
                <th class="text-left w-52"></th>
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
                                class="border p-2 w-full text-black text-center"
                            >
                                <option value="text">Text</option>
                                <option value="date">Date</option>
                            </select>
                        </td>
                        <td>
                            <div class="flex items-center space-x-4">
                                <div v-for="mode in $page.props.modes" :key="mode.id" class="flex items-center">
                                    <input
                                        type="checkbox"
                                        :value="mode.id"
                                        :checked="isModeSelected(mode.id, question)"
                                        @change="toggleMode(mode, question)"
                                    />
                                    <span class="ml-2">{{ mode.name }}</span>
                                </div>
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
                        <td class="text-center">{{ capitalizeFirstLetter(question.question_type) }}</td>
                        <td >
                            <span v-for="(mode, index) in question.modes" :key="mode.id" class="text-center">
                                {{ mode.name }}<span v-if="index < question.modes.length - 1">, </span>
                            </span>
                        </td>
                        <td class="text-right">
                            <SecondaryButton type="button" @click="editQuestion(index,question)">
                                Edit
                            </SecondaryButton>
                        </td>
                    </template>
                </tr>
            </template>
        </Table>

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
