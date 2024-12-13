<script setup>
import { computed } from 'vue';
import { formatDate } from '@/utils';

// Props for game and questions
const props = defineProps({
    questions: Array,
});

const answersMap = computed(() => {
    return props.answers.reduce((map, ans) => {
        map[ans.question_id] = ans.answer;
        return map;
    }, {});
});

const formatAnswer = (question, answer) => {

    if (question.question_type === 'date') {
        return formatDate(answer);
    }

    return answer;
};

</script>

<template>
    <div>
        <div v-for="question in questions" :key="question.id" class="mb-4 ml-4">
            <div class="font-bold">{{ question.question_text }}</div>

            <div v-for="answer in question.answers">
                {{ answer.game_user.user.first_name }} {{ answer.game_user.user.last_name }}:
                <span v-if="question.question_type === 'date'"> {{ formatDate(answer.answer) }} </span>
                <span v-else> {{ answer.answer }} </span>
            </div>
        </div>
    </div>
</template>
