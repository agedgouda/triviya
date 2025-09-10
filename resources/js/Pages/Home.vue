<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
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
  <Head title="Home" />

  <div class="min-h-screen flex flex-col bg-cover bg-center bg-no-repeat text-triviyaRegular" style="background-image: url('/images/triviya-bg-cover.png');">

    <main class="flex-grow">
      <div class="flex justify-center items-center mt-20">
        <ApplicationLogo />
      </div>
      <div class="bg-white shadow-md sm:rounded-lg p-6 mb-6 mx-auto max-w-xl rounded-xl text-black">
        <div class="mb-5 text-2xl font-bold">Let's get the party started!</div>
        <div class="mb-2">How it works:</div>
        <div>
          <span class="font-bold mt-2 text-center text-triviya-red text-xl">1</span>
          Create an account (or log in if you already have one)
        </div>
        <div>
          <span class="font-bold mt-2 text-center text-triviya-red text-xl">2</span>
          Add your game details name and location
        </div>
        <div>
          <span class="font-bold mt-2 text-center text-triviya-red text-xl">3</span>
          Invite players — share your unique link via email or group chat – whatever works best!
        </div>
        <div>
          <span class="font-bold mt-2 text-center text-triviya-red text-xl">4</span>
          Take the quiz - you’re not just the host, you’re also a player!
        </div>

        <div class="mb-2 text-center mb-4">
          <PrimaryButton type="button" class="mt-2" @click="login">
            <span>Login</span>
          </PrimaryButton>
          <PrimaryButton type="button" class="mt-2 ml-2" @click="register">
            <span>Register</span>
          </PrimaryButton>
        </div>
      </div>
    </main>

    <footer class="py-16 text-center text-sm text-white dark:text-white/70">
      TriviYa Copyright ©{{ new Date().getFullYear() }}
    </footer>
  </div>
</template>
