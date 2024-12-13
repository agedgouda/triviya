<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import CustomMaskInput from '@/Components/CustomMaskInput.vue';
import { usePage } from '@inertiajs/vue3';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
});

const props = defineProps({
    gameId: String,
    invtees: Object,
});
const page = usePage();

const submit = () => {
    form.post(route('games.invite', { game: props.gameId }), {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};

</script>

<template>
    <div class="mt-10 ml-10">
        <div class="mb-3 font-bold">Invite Players</div>
        <form @submit.prevent="submit">
            <div class="flex">
                <div class="mr-3">
                    <InputLabel for="first_name" value="First Name"/>
                    <TextInput
                        id="first_name"
                        v-model="form.first_name"
                        type="text"
                        class="mt-1 block w-full"
                        required

                    />
                    <InputError class="mt-2" :message="form.errors.first_name" />
                </div>
                <div class="mr-3">
                    <InputLabel for="last_name" value="Last Name" />
                    <TextInput
                        id="last_name"
                        v-model="form.last_name"
                        type="text"
                        class="mt-1 block w-full"
                        required

                    />
                    <InputError class="mt-2" :message="form.errors.first_name" />
                </div>
                <div class="mr-3">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-96"
                        required

                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div class="h-3 mt-2 ml-1 text-red-500">
                {{ $page.props.error }}
            </div>

            <div class="flex items-center justify-end mt-4">

                <PrimaryButton class="mr-10" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Invite Player
                </PrimaryButton>
            </div>
        </form>

    </div>
</template>
