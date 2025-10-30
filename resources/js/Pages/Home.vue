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
        <div class="mb-5 text-2xl font-bold">
            How to Play
        </div>
            <ol class="list-decimal mx-5 mb-5">
                <li>Answer <span class="font-bold">ten fun questions about yourself</span> — but don’t share! Everyone’s quiz is different.</li>
                <li><span class="font-bold">One of you is the Host</span> — they create the game and send the link.</li>
                <li><span class="font-bold">Everyone else joins as Players</span>, takes their quiz, and gets ready to guess who said that!</li>
                <li>When everyone’s done, it’s <span class="font-bold">devices down</span> — only the Host needs one to run the game.</li>
            </ol>

        <div class="text-center">
            <PrimaryButton type="button" class="mt-2" @click="login">
                <span>Login</span>
            </PrimaryButton>
            <PrimaryButton type="button" class="mt-2 ml-2" @click="register">
                <span>Register</span>
            </PrimaryButton>
        </div>
    </AuthenticationCard>


</template>
