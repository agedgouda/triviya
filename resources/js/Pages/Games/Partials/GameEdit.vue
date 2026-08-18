<script setup>
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { usePage } from '@inertiajs/vue3'


const props = defineProps({
    game: {
        type: Object,
        default: () => null, // Defaults to null for create mode
    },
    players: Array,
    modes: Array,
    routeName: String,
});

const page = usePage();

const goBack = () => {
    window.history.back();
}

const form = useForm({
    name: props.game?.name || '',
    location: props.game?.location || '',
    date_time: props.game?.date_time || '',
    mode_id: props.game?.mode_id || 1,
});

const submit = async () => {
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
    All fields are required
    <div class="mb-10">
        <div v-if="$page.props.errors" class="text-red-800 mb-3 text-lg">
            {{ $page.props.errors.message }}
        </div>


        <form @submit.prevent="submit">
            <div class="flex flex-col sm:flex-row">
                <div class="mr-0 md:mr-3">
                    <InputLabel for="name" value="Game Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full sm:w-96"
                        required
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mr-0 md:mr-3">
                    <InputLabel for="location" value="Location" />
                    <TextInput
                        id="location"
                        v-model="form.location"
                        type="text"
                        class="mt-1 block w-full sm:w-96"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.location" />
                </div>
            </div>
            <div class="flex flex-col sm:flex-row">
                <div class="flex items-center mt-10">

                    <SecondaryButton type="button" class="mr-3" @click="goBack">
                        <span>Back</span>
                    </SecondaryButton>
                    <PrimaryButton  :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ routeName == 'games.create' ? 'Continue' : 'Update Game'  }}
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>
