<script setup>
import { computed } from 'vue';
import { formatDate } from '@/utils';

// Props for game and questions
const props = defineProps({
    game: Object,
    questions: Array,
    answers: Array,
});

const answersMap = computed(() => {
    return props.answers.reduce((map, ans) => {
        map[ans.question_id] = ans.answer;
        return map;
    }, {});
});

const formatAnswer = (question) => {
    const answer = answersMap.value[question.id];

    if (!answer) {
        return 'Not yet answered';
    }

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
            {{ formatAnswer(question) }}
        </div>
    </div>
</template>
