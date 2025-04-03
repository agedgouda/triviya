<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome" />

    <div class="relative min-h-screen bg-cover bg-center bg-no-repeat text-triviusRegular" style="background-image: url('/images/trivius-bg-cover.png');">

        <!--<img id="background" class="absolute -left-20 top-0 bg-cover" src="/images/trivius-bg-cover.png" />-->
        <header class="grid grid-cols-2 items-center gap-2 pb-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">

                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 py-10 pr-10 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Dashboard
                        </Link>
                        <!--
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </template>
                        -->
                    </nav>
                </header>
        <div class="relative min-h-screen flex flex-col items-center justify-center ">
            <main class="h-96">
                <div class="justify-center items-center">
                    <ApplicationLogo />
                </div>
                <div class="text-center text-3xl pt-10 font-bold text-white">The party game where you become the trivia!</div>
            </main>

            <footer class="py-16 text-center text-sm text-white dark:text-white/70">
                TriviYa Copyright ©{{ new Date().getFullYear() }}
            </footer>
        </div>
    </div>
</template>
