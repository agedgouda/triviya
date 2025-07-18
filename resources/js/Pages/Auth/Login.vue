<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue'
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import PasswordInput from '@/Components/PasswordInput.vue';

const props = defineProps({
    canResetPassword: Boolean,
    status: String,
    flash: Object,
    game: {
        type: Object,
        default: () => ({}),
    },
    user: {
        type: Object,
        default: () => ({}),
    },
    redirect_to: {
        type: String,
        default: '',
    },
});

const show = ref(false)

const form = useForm({
    email: props.user.email || '',
    game: props.game || '',
    user: props.user || '',
    redirect_to: props.redirect_to || '',
    password: '',
    remember: false,
});

const submit = () => {
    const postRoute = Object.keys(props.game).length === 0 ? 'login' : 'login.submit'
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route(postRoute, [props.game.id, props.user.id] ), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-2 font-medium text-sm">
            {{ status }}
        </div>
        <div v-if="flash" class="mb-2 font-medium text-sm">
            {{ flash.message }}
        </div>

        <form @submit.prevent="submit">


            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-2">
                <InputLabel for="password" value="Password" />
                <PasswordInput
                    id="password"
                    name="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-2">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-triviyaRegular">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-triviyaRegular hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Forgot your password?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
