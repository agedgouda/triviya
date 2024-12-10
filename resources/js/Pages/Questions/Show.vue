<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import QuestionsList from './Partials/QuestionsList.vue';

const props = defineProps({
    questions: Object,
    routeName: String,
    modes: Array,
    error: String,
});


</script>

<template>
    <AppLayout title="Questions">


        <template #header>

                Questions

        </template>


        <div class="p-5">

            <template v-if="routeName === 'questions'">
                <div>
                    <QuestionsList :questions="questions" />
                </div>
            </template>
        </div>

    </AppLayout>
</template>
