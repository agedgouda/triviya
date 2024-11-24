<script setup>
import { formatDate } from '@/utils';
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Invitees from './Invitees.vue';
import { MaskInput } from "vue-mask-next";

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone_number: '',
});

const props = defineProps({
    gameId: Number,
    invtees: Object,
});


const submit = () => {
    console.log(form)
    /*form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });*/
};

</script>

<template>
    <div>
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="first_name" value="First Name" />
                <TextInput
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="first_name"
                />
                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>
            <div>
                <InputLabel for="last_name" value="Last Name" />
                <TextInput
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="last_name"
                />
                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="phone_number" value="Mobile Number" />
                <MaskInput
                    id="phone_number"
                    v-model="form.phone_number"
                    mask="(###) ###-####"
                    type="tel"
                    class="mt-1 block w-full"
                    required
                    autocomplete="tel"
                />
                <InputError :message="form.errors.phone_number" class="mt-2" />
            </div>
            <div class="flex items-center justify-end mt-4">

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Invite Player
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>