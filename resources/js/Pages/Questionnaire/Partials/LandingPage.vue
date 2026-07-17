<script setup>
import { useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import QuestionsLayout from '@/Layouts/QuestionsLayout.vue';

// Props for game and questions
const props = defineProps({
    game: Object,
    user: Object,
});

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('questions.join', props.game.id));
};

</script>

<template>
    <QuestionsLayout title="Questions" :header-has-background="false">

        <template #raw-header>
            <div class="raw-header mt-8 mb-8">
                <h3 class="game-author text-center text-white font-bold text-xl" v-text="game.host[0].name"></h3>
                <h4 class="text-center text-white text-lg">has invited you to play</h4>
                <div class="extended-logo">
                    <ApplicationLogo class="flex justify-center block !h-32 mx-auto w-auto" />
                </div>
            </div>
        </template>

        <template #question-input>
            <div class="mb-2 text-center text-xl font-bold border-b-2 pb-4">Here's how it works</div>
            <ul class="list-disc mx-12 mb-2">
                <li class="mb-2">Everyone invited to the game will answer 10 questions about themselves</li>
                <li class="mb-2">During the game, teams will guess who said what</li>
                <li class="mb-2">Teams earn points matching the answers to the correct people</li>
                <li>The team with the most points wins</li>
            </ul>

            <form @submit.prevent="submit" class="w-full max-w-md mx-auto">
                <InputLabel for="name" value="Enter your name to join" />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.name" class="mt-2" />

                <div class="mb-2 text-center b-4">
                    <PrimaryButton type="submit" class="mt-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        <span>Join Game</span>
                    </PrimaryButton>
                </div>
            </form>
        </template>

    </QuestionsLayout>
</template>
