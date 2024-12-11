<script setup>
import { capitalizeFirstLetter } from '@/utils';
import { router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';
import Table from '@/Components/Table.vue';
import AddQuestion from './AddQuestion.vue';


const props = defineProps({
    questions: Object,
    pageSize: {
        type: Number,
        default: 25, // Default page size
    }
});

const currentPage = ref(1);
const selectedMode = ref(1);
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
const filteredQuestions = computed(() => {
  const questions = props.questions;
  return questions.filter(question => {
    return question.modes.some(mode => mode.id === selectedMode.value);
  });
});

// Paginate filtered questions
const paginatedQuestions = computed(() => {
  const start = (currentPage.value - 1) * props.pageSize;
  const end = start + props.pageSize;
  return filteredQuestions.value.slice(start, end);
});

// Calculate total pages
const totalPages = computed(() => {
  return Math.ceil(filteredQuestions.value.length / props.pageSize);
});

// Navigation
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

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

        <Modal :show=addQuestion><AddQuestion @close-add-question="closeAddQuestion"/></Modal>

        <div class="flex items-center gap-4 mb-3 w-full">
            <div v-for="mode in $page.props.modes" :key="mode.id">
                <input type="radio" :value="mode.id" v-model="selectedMode" class="text-amber-600 focus:ring-teal-700 "/>
                <span class="ml-1">{{ mode.name }}</span>
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
                    v-for="(question, index) in paginatedQuestions"
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

    <div v-if="totalPages > 1">
        <div class="mt-4 flex items-center justify-center">
            <!-- Previous Button (<<) -->
            <button
                :disabled="currentPage === 1"
                @click="goToPage(1)"
                class="inline-flex items-center px-4 py-2 text-sm font-bold text-amber-500 bg-teal-700 border border-amber-500 hover:bg-amber-700 disabled:text-amber-300 disabled:border-amber-500 disabled:bg-teal-700"
            >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
            </svg>

            </button>

            <button
                :disabled="currentPage === 1"
                @click="goToPage(currentPage - 1)"
                class="inline-flex items-center px-4 py-2 text-sm font-bold text-amber-500 bg-teal-700 border border-l-0 border-amber-500 hover:bg-amber-700 disabled:text-amber-300 disabled:border-amber-500 disabled:bg-teal-700"
            >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>

                <span class="sr-only">Previous</span>
            </button>

            <!-- Page Numbers -->
            <template v-for="page in totalPages" :key="page">
                <button
                @click="goToPage(page)"
                :class="[
                    'inline-flex items-center px-4 py-2 text-sm font-bold',
                    page === currentPage
                    ? 'bg-amber-700 text-amber-500 border-amber-500'
                    : 'text-amber-500 bg-tea-700 border-amber-500 hover:bg-amber-700 ',
                    'border border-l-0',
                    'disabled:text-gray-300 disabled:border-gray-300 disabled:bg-white'
                ]"
                >
                {{ page }}
                </button>
            </template>

            <!-- Next Button (>>) -->
            <button
                :disabled="currentPage === totalPages"
                @click="goToPage(currentPage + 1)"
                class="inline-flex items-center px-4 py-2 text-sm font-bold text-amber-500 bg-teal-700 border border-l-0 border-amber-500 hover:bg-amber-700 disabled:text-amber-300 disabled:border-amber-500 disabled:bg-teal-700"
            >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>

                <span class="sr-only">Next</span>
            </button>

            <!-- Next Page (>>) Button -->
            <button
                :disabled="currentPage === totalPages"
                @click="goToPage(totalPages)"
                class="inline-flex items-center px-4 py-2 text-sm font-bold text-amber-500 bg-teal-700 border border-l-0 border-amber-500 hover:bg-amber-700 disabled:text-amber-300 disabled:border-amber-500 disabled:bg-teal-700"
            >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
            </svg>

            </button>
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
