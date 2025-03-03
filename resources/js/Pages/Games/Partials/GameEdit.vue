<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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
    location: props.game?.location || '',
    date: props.game ? props.game.date_time.split(' ')[0] : '',
    time: props.game ? props.game.date_time.split(' ')[1] : '',
    date_time: props.game?.date_time || '',
    mode_id: props.game?.mode_id || 1,
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

const availableHours = [
            { value: "09:00:00", label: "9:00 AM" },
            { value: "10:00:00", label: "10:00 AM" },
            { value: "11:00:00", label: "11:00 AM" },
            { value: "12:00:00", label: "12:00 PM" },
            { value: "13:00:00", label: "1:00 PM" },
            { value: "14:00:00", label: "2:00 PM" },
            { value: "15:00:00", label: "3:00 PM" },
            { value: "16:00:00", label: "4:00 PM" },
            { value: "17:00:00", label: "5:00 PM" },
            { value: "18:00:00", label: "6:00 PM" },
            { value: "19:00:00", label: "7:00 PM" },
            { value: "20:00:00", label: "8:00 PM" },
            { value: "21:00:00", label: "9:00 PM" },
        ];

</script>


<template>
    All fields are required
    <div class="mb-10">
        <div v-if="$page.props.errors" class="text-red-800 mb-3 text-lg">
            {{ $page.props.errors.message }}
        </div>


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
                <!--
                <div class="mr-3">
                    <InputLabel for="mode_id" value="Mode" />
                    <select
                        id="mode_id"
                        v-model="form.mode_id"
                        class="mt-1 block w-96 text-black"
                        required
                        >
                        <option value="" disabled>Select a Mode</option>


                        <option
                            v-for="mode in modes"
                            :key="mode.id"
                            :value="mode.id"
                        >
                            {{ mode.name }}
                        </option>
                    </select>

                    <InputError class="mt-2" :message="form.errors.mode_id" />
                </div> -->
            </div>

            <div class="flex mt-3">
                <div class="mr-5">
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

                <div class="mr-5">
                    <InputLabel for="time" value="Game Time" />
                    <select
                        id="time"
                        v-model="form.time"
                        class="mt-1 block w-36 text-black border rounded p-2"
                        required
                    >
                        <option disabled value="">Select a time</option>
                        <option v-for="hour in availableHours" :key="hour.value" :value="hour.value">
                            {{ hour.label }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.time" />
                </div>
                <div class="flex items-center mt-6">
                    <PrimaryButton  :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ routeName == 'games.create' ? 'Continue' : 'Update Game'  }}
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>
