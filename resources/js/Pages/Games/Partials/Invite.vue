<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref,reactive } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const defaultForm = useForm({
    first_name: '',
    last_name: '',
    email: '',
});

const form = reactive({ ...defaultForm });

const props = defineProps({
    gameId: String,
});

const submit = () => {
    form.post(route('games.createUser', { game: props.gameId }), {
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
    <div class="mt-5">
        <div class="mb-3 font-bold">Invite Players</div>
        <form @submit.prevent="submit">
            <div class="flex">
                <div class="mr-3">
                    <InputLabel for="first_name" value="First Name"/>
                    <TextInput
                        id="first_name"
                        v-model="form.first_name"
                        type="text"
                        class="block w-full"
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
                        class="block w-full"
                        required

                    />
                    <InputError class="mt-2" :message="form.errors.last_name" />
                </div>
                <div class="mr-3">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="block w-96"
                        required

                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton class="mr-10" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Invite Player
                    </PrimaryButton>
                </div>
            </div>
        </form>
        <div class="h-3 mt-2 text-red-500">
            {{ $page.props.error }}
        </div>
    </div>
</template>
