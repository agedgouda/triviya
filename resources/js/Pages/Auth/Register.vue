<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import PasswordInput from '@/Components/PasswordInput.vue';

const props = defineProps({
    game: {
        type: Object,
        default: () => ({}), // Ensure a default empty object
    },
    user: {
        type: Object,
        default: () => ({}), // Ensure a default empty object
    },
    redirect_to: {
        type: String,
        default: '',
    },
    flash: Object,
});

const form = useForm({
    first_name: props.user.first_name || '',
    last_name: props.user.last_name || '',
    email: props.user.email || '',
    redirect_to: props.redirect_to || '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

onMounted(() => {
    if(form.password.length < 8) {
        form.errors.password = "Password must be at least 8 characters";
    }
});

watch(
  [() => form.password, () => form.password_confirmation],
  ([newPassword, newPasswordConfirmation], [oldPassword, oldPasswordConfirmation]) => {
    // Handle changes to form.password
    if(newPassword.length >= 8) {
      form.errors.password = "";
    } else {
      form.errors.password = "Password must be at least 8 characters";
    }

    if(newPasswordConfirmation && newPassword !== newPasswordConfirmation) {
      form.errors.password_confirmation = "Passwords do not match";
    } else {
      form.errors.password_confirmation = "";
    }
  }
);

const submit = () => {

    if(props.user) {
        form.post(route('register.submit',  [props.game.id, props.user.id]), {
                onSuccess: () => {
                    console.log('Registration successful!');
                },
            });
    }
    else {
        form.post(route('register.submit'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    }
};
</script>

<template>
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="flash" class="mb-2 font-medium text-sm text-triviyaPink">
            {{ flash.message }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="first_name" value="First Name" />
                <TextInput
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="block w-full"
                    required
                    autocomplete="first_name"
                />
                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>
            <div class="mt-3">
                <InputLabel for="last_name" value="Last Name" />
                <TextInput
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="block w-full"
                    required
                    autocomplete="last_name"
                />
                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <div class="mt-3">
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

            <div class="mt-3">
                <InputLabel for="password" value="Password" />

                <PasswordInput
                    id="password"
                    v-model="form.password"
                    class="block w-full"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-3">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <div class="mt-1">
                    Your email is used to create and manage your games and to send you TriviYa updates. It will not be visible to other players. You can unsubscribe from updates anytime.
                </div>
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-triviyaRegular hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terms of Use</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-triviyaRegular hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>
            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-triviyaRegular hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
