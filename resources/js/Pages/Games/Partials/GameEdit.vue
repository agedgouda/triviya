<script setup>
import { formatDate } from '@/utils';
import { ref,watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { DatePicker } from 'v-calendar';
import 'v-calendar/style.css';

const props = defineProps({
    game: {
        type: Object,
        default: () => null, // Defaults to null for create mode
    },
    players: Array,
    modes: Array,
    routeName: String,
});

const changeDate = ref(false);
const changeForm = ref(false);



const form = useForm({
    name: props.game?.name || '',
    location: props.game?.location || '',
    date_time: props.game?.date_time || '',
    mode_id: props.game?.mode_id || null,
});


const submit = async () => {
    try {
        if (props.routeName === 'games.create') {
            await form.post(route('games.store'), {
                preserveScroll: true, // Prevent scroll reset
            });
        } else {
            await form.put(route('games.update', props.game), {
                preserveScroll: true, // Prevent scroll reset
            });
        }
    } catch (error) {
        if (error.response?.status === 422) {
            // Extract and assign validation errors to form.errors
            form.errors = error.response.data.errors;

            // Log each error to the console for visibility
            Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                console.error(`${field}: ${messages.join(', ')}`);
            });
        } else {
            console.error('Error submitting form:', error);
        }
    }
};

const toggleChangeDate = () => {
    console.log("freem")
    changeDate.value = !changeDate.value;
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
                    <InputLabel for="location" value="Location" />
                    <TextInput
                        id="location"
                        v-model="form.location"
                        type="text"
                        class="mt-1 block w-96"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.location" />
                </div>
                <div class="mt-6">
                    <DatePicker v-model="form.date_time" mode="dateTime" hide-time-header :time-accuracy="2"/>
                    <InputError class="mt-2" :message="form.errors.date_time" />
                </div>

                <div class=" mt-6 ml-5">
                <PrimaryButton  :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ routeName == 'games.create' ? 'Add Game' : 'Save Changes'  }}
                </PrimaryButton>
                </div>

            </div>

        </form>


    </div>
</template>