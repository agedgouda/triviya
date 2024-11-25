<script setup>
import { formatDate } from '@/utils';
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    game: Object,
    players: Array,
    modes: Array
});

const form = useForm({
    name: '',
    date: '',
    time: '',
    date_time: '',
    mode_id: null,
});

const submit = async () => {
    // Combine the date and time into a single date_time field

    form.date_time = `${form.date}T${form.time}`;

    try {
        const response = await form.post(route('games.store'), {
            preserveScroll: true, // Prevent scroll reset
            onSuccess: ({ props }) => {
                const gameId = props.flash?.gameId;
                if (gameId) {
                    router.visit(route('games.show', gameId));
                }
            },
        });
    } catch (error) {
        console.error('Error submitting form:', error);
    }
};

</script>


<template>
    <div class="m-10">
        <form @submit.prevent="submit">
            <div class="flex">
                <div class="mr-3">
                    <InputLabel for="name" value="Game Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-96"
                        required
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div class="mr-3">
                    <InputLabel for="mode_id" value="Mode" />
                    <select
                        id="mode_id"
                        v-model="form.mode_id"
                        class="mt-1 block w-96"
                        required
                        >
                        <!-- Optional placeholder -->
                        <option value="" disabled>Select a Mode</option>

                        <!-- Dynamically generate options from modes array -->
                        <option
                            v-for="mode in modes"
                            :key="mode.id"
                            :value="mode.id"
                        >
                            {{ mode.name }}
                        </option>
                    </select>

                    <InputError class="mt-2" :message="form.errors.mode_id" />
                </div>
            </div>
            <div class="flex mt-3">
                <div class="mr-3">
                    <InputLabel for="date" value="Game Date" />
                    <TextInput
                        id="date"
                        v-model="form.date"
                        type="date"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.date" />
                </div>

                <div class="mr-3">
                    <InputLabel for="time" value="Game Time" />
                    <TextInput
                        id="time"
                        v-model="form.time"
                        type="time"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.time" />
                </div>
                <div class="flex items-center mt-6">
                    <PrimaryButton  :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Add Game
                    </PrimaryButton>
                </div>
            </div>
        </form>


    </div>
</template>