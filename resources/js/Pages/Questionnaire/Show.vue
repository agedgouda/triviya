<script setup>
import { ref } from 'vue';
import { formatDate } from '@/utils';
import { useForm } from '@inertiajs/vue3';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';
import PlayerQuestions from '@/Components/PlayerQuestions.vue';

// Import components
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
    questions: Array,
    answers: Array,
});

const error = ref();

// Create a reactive form object using useForm
const form = useForm({
    answers: {}, // Store answers keyed by question ID
});

// Populate form.answers based on the answers prop
if (props.answers && props.answers.length > 0) {
    const initialAnswers = {};
    props.answers.forEach(answer => {
        initialAnswers[answer.question_id] = answer.answer;
    });
    form.answers = initialAnswers; // Replace form.answers with a reactive object
}

// Submit answers function
const submitAnswers = () => {
    form.post(route('questions.playerAnswers', { game: props.game.id, user: props.user.id }), {
        onSuccess: (response) => {
            console.log(response);
        },
        onError: (errors) => {
            error.value = errors.message;
        },
    });
};
</script>

<template>
    <QuestionsLayout title="Questions">
    <template #header>
        <div>{{game.name}}</div>
        <div class="text-base">Hosted by {{ game.host[0].first_name }} {{ game.host[0].last_name }}</div>
        <div class="text-base">{{ formatDate(game.date_time) }}</div>
        <div class="text-base">{{ game.location }}</div>
    </template>

    <div class="p-5">
        <div class="ml-4 mb-4">
            Welcome, {{ user.first_name }} {{ user.last_name }}.
            <div v-if="!user.has_registered">
                Once you've completed your quiz you will be able to register your account change any answers before the game as well as host your own game!
            </div>
        </div>
        <PlayerQuestions :questions="questions" :answers="answers" :game="game" :user="user" />
    </div>
</QuestionsLayout>
</template>
