<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePage } from '@inertiajs/vue3'

const page = usePage()


const login = () => {
    router.visit(route('login.prepopulated'));
};
const register = () => {
    router.visit(route('register.prepopulated'));
};

</script>

<template>

    <Flash />
    <Head title="Home" />
    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>
        <div class="mb-5 text-2xl text-center font-bold">
            Who Are You?
        </div>
        <div class="border border-triviya-red sm:rounded-lg p-4  mb-5">
            <div class="font-bold">
            The Host?
            </div>
            You’ll kick off the game, share the link, and keep things moving. Once everyone’s finished their quiz, it’s devices down. Only you need a screen. Log in or register to start.


            <div class="text-center">
                <PrimaryButton type="button" class="mt-2" @click="login">
                    <span>Login</span>
                </PrimaryButton>
                <PrimaryButton type="button" class="mt-2 ml-2" @click="register">
                    <span>Register</span>
                </PrimaryButton>
            </div>
        </div>
        <div class="border border-triviya-red sm:rounded-lg p-4  mb-5">
            <div>
                Or the <span class="font-bold">Player</span>?
            </div>
            Hang tight. Your host will send your TriviYa quiz link by text, email, or chat. Open it, answer the questions and you’re in.

        </div>
    </AuthenticationCard>


</template>
