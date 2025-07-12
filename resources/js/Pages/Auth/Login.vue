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

                    <div class="relative">
                        <TextInput
                            id="password"
                            v-model="form.password"
                            :type="show ? 'text' : 'password'"
                            class="block w-full pr-10"
                            required
                            autocomplete="current-password"
                        />
                            <button
                                type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                                aria-label="Toggle password visibility"
                            >
                                <svg v-if="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.966 9.966 0 012.563-4.303M9.88 9.88a3 3 0 104.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3l18 18" />
                                </svg>
                            </button>
                    </div>

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
