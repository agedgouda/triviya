<script setup>
import { ref, watch, reactive,computed } from 'vue';
import { useForm,usePage,router } from '@inertiajs/vue3';

import LandingPage2 from './LandingPage2.vue';
import axios from 'axios';

// Import components
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';


// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
});
const { props: pageProps } = usePage();
const error = ref();
const questionNumber = ref(0);

const question = computed(() => props.questions[questionNumber.value - 1]);

// Create a reactive form object using useForm
const form2 = useForm({
    answers: {}, // Store answers keyed by question ID
});

const form = reactive({
    questionId: null,
    answer: null
});


// Submit answers function
const submitAnswers = async () => {
    try {
        const response = await axios.post(
            route('questions.playerAnswer', { game: props.game.id, user: props.user.id }),
            { question: question.value } // assuming you're sending this as data
        );

console.log(props.questions.length)
        questionNumber.value + 1 <= props.questions.length
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
    <LandingPage2 :questions="questions" :game="game" :user="user" v-if="questionNumber === 0" @start-game="questionNumber = 1"/>
    <QuestionsLayout title="Questions" v-else>
        <template #question-header>
            <div class="ml-4 mb-4 text-red-700" v-if="error">{{ error }}</div>

            <div class="mb-4">
                <div class="mb-2 text-triviya-red text-lg font-bold">
                    Question {{ questionNumber }} of {{ questions.length }}
                </div>
                <div class="mb-2 text-white">
                    <InputLabel :for="'question-' + question.id" :value="question.question_text" size-class="text-2xl"/>
                </div>
            </div>
        </template>

        <template #question-input>
            <div>
                <TextInput
                    :id="'question-' + question.id"
                    type="text"
                    placeholder="Enter your answer"
                    v-model="question.answer"
                    required
                    class="mt-1 block w-full"
                />
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
                    <span v-if="questionNumber <= questions.length">Next</span>
                    <span v-else>End</span>
                </PrimaryButton>
            </form>
        </template>

    </QuestionsLayout>
</template>
