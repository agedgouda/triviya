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
    game: {
        type: Object,
        default: () => null, // Defaults to null for create mode
    },
    players: Array,
    modes: Array,
    routeName: String,
});

const form = useForm({
    name: props.game?.name || '',
    date: props.game ? props.game.date_time.split(' ')[0] : '',
    time: props.game ? props.game.date_time.split(' ')[1] : '',
    date_time: props.game?.date_time || '',
    mode_id: props.game?.mode_id || null,
});

const submit = async () => {
    // Combine the date and time into a single field
    form.date_time = `${form.date}T${form.time}`;

    
        try {
            // Submit the form
            if(props.routeName == 'games.create') {
                await form.post(route('games.store'), {
                    preserveScroll: true, // Prevent scroll reset
                });
            } else {

                await form.put(route('games.update',props.game), {
                    preserveScroll: true, // Prevent scroll reset
                });
            }
            // No further action needed; the server will redirect to games.show
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
                        {{ routeName == 'games.create' ? 'Add' : 'Update'  }} Game
                    </PrimaryButton>
                </div>
            </div>
        </form>


    </div>
</template>