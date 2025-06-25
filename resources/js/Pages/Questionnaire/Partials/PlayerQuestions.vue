<script setup>
import { ref, watch, reactive,computed } from 'vue';
import { useForm,usePage,router } from '@inertiajs/vue3';
import axios from 'axios';

// Import components
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { formatDate } from '@/utils';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';


// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
});
const { props: pageProps } = usePage();
const error = ref();
const questionNumber = ref(0);

const question = computed(() => props.questions[questionNumber.value]);

// Create a reactive form object using useForm
const form2 = useForm({
    answers: {}, // Store answers keyed by question ID
});

const form = reactive({
    questionId: null,
    answer: null
});

const questionDateValues = reactive({});

/***********************
 *
 *  Date Dropdowns
 *
************************/
// Month names
const months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

// Function to calculate days in a selected month and year
const getDaysInMonth = (month, year) => {
  return new Date(year, month, 0).getDate();
};

// Computed property for days in a month for each question
const daysInMonth = (questionId) => {
  const { selectedMonth, selectedYear } = questionDateValues[questionId];
  return Array.from({ length: getDaysInMonth(selectedMonth, selectedYear) }, (_, i) => i + 1);
};

// Array for years (2020 going back to 1920)
const years = Array.from({ length: 101 }, (_, i) => 2020 - i);

//populate date dropdowns with current date or previous answer
props.questions
  .filter((question) => question.question_type === 'date')
  .forEach((question) => {
    const existingValue = form.answers[question.id];

    if (existingValue) {
      // If form.answers[question.id] exists, parse the YYYY-MM-DD value
      const [year, month, day] = existingValue.split('-').map(Number);

      questionDateValues[question.id] = {
        selectedYear: year || 2020,                 // Use parsed year, fallback to 2020
        selectedMonth: month || new Date().getMonth() + 1, // Use parsed month, fallback to current
        selectedDay: day || new Date().getDate(),   // Use parsed day, fallback to current
      };
    } else {
      // Default initialization if no value exists
      questionDateValues[question.id] = {
        selectedYear: 2020,                      // Default year
        selectedMonth: new Date().getMonth() + 1, // Default to current month
        selectedDay: new Date().getDate(),       // Default to current day
      };
    }
  });

// Update date answer in form.
watch(
  () => questionDateValues,
  () => {
    Object.entries(questionDateValues).forEach(([questionId, { selectedMonth, selectedDay, selectedYear }]) => {
      form.answers[questionId] = `${selectedYear}-${String(selectedMonth).padStart(2, '0')}-${String(
        selectedDay
      ).padStart(2, '0')}`;
    });
  },
  { deep: true, immediate: true }
);


// Submit answers function
const submitAnswers = async () => {
    try {
        const response = await axios.post(
            route('questions.playerAnswer', { game: props.game.id, user: props.user.id }),
            { question: question.value } // assuming you're sending this as data
        );


        questionNumber.value + 1 < props.questions.length
        ? questionNumber.value++
        : router.visit(route('questions.showThankYou', { game: props.game.id, user: props.user.id }));


    } catch (error) {
        console.error(`Failed to save changes for ${question.value.id}:`, error.response?.data || error.message);
    }
};

const answeredCount = computed(() => props.questions.filter(q => q.answer !== null).length);

const changeQuestion = (increment) => {
    questionNumber.value += increment;
}

</script>

<template>
    <QuestionsLayout title="Questions">
<!--        <template #header>-->
<!--            <ApplicationLogo class="flex justify-center block !h-20 mx-auto w-auto" />-->
<!--        </template>-->

        <template #question-header>
            <div class="ml-4 mb-4 text-red-700" v-if="error">{{ error }}</div>

            <div class="mb-4">
                <div class="mb-2 text-triviya-red text-lg font-bold">
                    Question {{ questionNumber + 1 }} of {{ questions.length }}
                </div>
                <div class="mb-2 text-white">
                    <InputLabel :for="'question-' + question.id" :value="question.question_text" size-class="text-2xl"/>
                </div>
            </div>
        </template>

        <template #question-input>
            <div>
                <template v-if="question.question_type === 'text'">
                    <TextInput
                        :id="'question-' + question.id"
                        type="text"
                        placeholder="Enter your answer"
                        v-model="question.answer"
                        required
                        class="mt-1 block w-full"
                    />
                </template>

                <template v-else-if="question.question_type === 'date'">
                    <div class="flex">
                        <!-- Month -->
                        <select
                            :id="'month-' + question.id"
                            v-model="questionDateValues[question.id].selectedMonth"
                            class="mt-1 block w-34 text-black"
                        >
                            <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                {{ month }}
                            </option>
                        </select>

                        <!-- Day -->
                        <select
                            :id="'day-' + question.id"
                            v-model="questionDateValues[question.id].selectedDay"
                            class="mt-1 block w-24 text-black ml-2"
                        >
                            <option v-for="day in daysInMonth(question.id)" :key="day" :value="day">
                                {{ day }}
                            </option>
                        </select>

                        <!-- Year -->
                        <select
                            :id="'year-' + question.id"
                            v-model="questionDateValues[question.id].selectedYear"
                            class="mt-1 block w-32 text-black ml-2"
                        >
                            <option v-for="year in years" :key="year" :value="year">
                                {{ year }}
                            </option>
                        </select>
                    </div>
                </template>

                <InputError :message="form.errors" class="mt-2" />
            </div>
        </template>

        <template #question-buttons>
            <form @submit.prevent="submitAnswers" class="flex items-center space-x-4">
                <SecondaryButton
                    type="button"
                    class="mt-2"
                    @click="changeQuestion(-1)"
                    :disabled="questionNumber === 0"
                >
                    Previous
                </SecondaryButton>

                <PrimaryButton type="submit" class="mt-2" :disabled="question.answer === null || question.answer.length === 0 ">
                    <span v-if="questionNumber + 1 < questions.length">Next</span>
                    <span v-else>End</span>
                </PrimaryButton>
            </form>
        </template>

    </QuestionsLayout>
</template>
