<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import Modal from '@/Components/Modal.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Flash from '@/Components/Flash.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const showAboutModal = ref(false);
const howToPage = ref(1);


const logout = () => {
    router.post(route('logout'));
};
const changeHowToPage = (direction) => {
    howToPage.value += direction;
};
</script>

<template>
<Modal
    :show="showAboutModal"
    title="How To Play"
    @close="showAboutModal = false"
    class="font-bold w-screen h-screen max-w-none m-0 p-0  flex-col"
>
    <div class="flex-1 overflow-y-auto p-6 text-sm">
        <!-- PAGE 1 -->
        <div v-if="howToPage === 1">
            <h2 class="text-lg mb-2">What You’ll Need</h2>
            <ul class="list-disc ml-5">
                <li>1 host</li>
                <li>4–12 players</li>
                <li>Phone, tablet, computer</li>
                <li>No downloads needed</li>
                <li>
                    A scorecard <span class="font-normal">(notes app or pen & paper)</span>
                </li>
            </ul>
        </div>

        <!-- PAGE 2 -->
        <div v-if="howToPage === 2">
            <h2 class="text-lg mb-2">Setting Up the Game</h2>
            <ul class="list-disc ml-5">
                <li>Host creates a game</li>
                <li>
                    Host shares the link
                    <span class="font-normal">with players</span>
                </li>
                <li>
                    Players each answer 10 questions
                    <span class="font-normal">before the game starts</span>
                </li>
                <li>
                    Only the host needs a device
                    <span class="font-normal">during gameplay</span>
                </li>
            </ul>
        </div>

        <!-- PAGE 3 -->
        <div v-if="howToPage === 3">
            <h2 class="text-lg mb-2">Game Play</h2>
            <ul class="list-disc ml-5">
                <li>Form teams <span class="font-normal">(pairs work best)</span></li>
                <li>Host reads each question</li>
                <li>Teams guess who said what</li>
                <li>Host sees answers at the same time as players</li>
                <li>Keep your own score</li>
                <li>After 3 rounds, highest score wins</li>
            </ul>
        </div>
    </div>

    <!-- BUTTON ROW (stick to bottom) -->
    <div class="flex justify-between w-full px-6 pb-6">
        <PrimaryButton
            @click="changeHowToPage(-1)"
            :disabled="howToPage === 1"
        >
            Previous
        </PrimaryButton>

        <PrimaryButton
            @click="changeHowToPage(1)"
            :disabled="howToPage === 3"
        >
            Next
        </PrimaryButton>
    </div>
</Modal>

    <div>
        <Flash />
        <Head :title="title" />

        <Banner />

        <div class="relative min-h-screen bg-cover bg-center bg-no-repeat text-black" style="background-image: url('/images/triviya-bg-cover.png');">
            <div class="w-3/4 mx-auto">
                <nav>
                    <!-- Primary Navigation Menu -->
                    <div class="px-4 sm:px-5 lg:px-6">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('games')">
                                        <ApplicationMark class="block h-9 w-auto" />
                                    </Link>
                                </div>

                                <!-- Navigation Links -->
                                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    <NavLink :href="route('games')" :active="route().current('games*')" :hasSubnav="false">
                                        My Games
                                    </NavLink>
                                    <NavLink @click="showAboutModal = true">
                                        About TriviYa
                                    </NavLink>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <!-- Settings Dropdown -->
                                <div class="ms-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                <img class="size-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.first_name+' '+$page.props.auth.user.last_name">
                                            </button>

                                            <span v-else class="inline-flex rounded-md">
                                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                    {{ $page.props.auth.user.first_name }} {{ $page.props.auth.user.last_name }}

                                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>

                                            <DropdownLink :href="route('profile.show')">
                                                Profile
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                                API Tokens
                                            </DropdownLink>

                                            <!-- Authentication -->
                                            <form @submit.prevent="logout">
                                               <DropdownLink as="button">
                                                    Log Out
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-me-2 flex items-center sm:hidden">
                                <button class="inline-flex items-center justify-center p-2 rounded-md text-triviya-red hover:text-gray-500 hover:bg-triviya-lightRed focus:outline-none focus:bg-lightRed focus:text-triviya-red transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                    <svg
                                        class="size-6"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                        <path
                                            :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200 border-triviyaLight">
                            <div class="flex items-center px-4">
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                    <img class="size-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.first_name+' '+$page.props.auth.user.last_name">
                                </div>

                                <div>
                                    <div class="font-medium text-base text-white">
                                        {{ $page.props.auth.user.first_name }} {{ $page.props.auth.user.last_name }}
                                    </div>
                                    <div class="font-medium text-sm text-white">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('games')" :active="route().current('games*')">
                                    My Games
                                </ResponsiveNavLink>

                                <ResponsiveNavLink @click="showAboutModal = true" rel="noopener">
                                    About TriviYa
                                </ResponsiveNavLink>

                                <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                    Profile
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                    API Tokens
                                </ResponsiveNavLink>

                                <!-- Authentication -->
                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button">
                                        Log Out
                                    </ResponsiveNavLink>
                                </form>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading -->
                <header v-if="$slots.header">
                    <div class="max-w-7xl px-5 sm:px-4 lg:px-10 text-blue-50">
                        <h2 class="font-semibold text-xl">
                            <slot name="header" />
                        </h2>
                    </div>
                </header>

                <!-- Page Content -->
                <main>
                    <div class="">
                        <div class="mx-auto sm:px-4 lg:px-8">
                            <div class="bg-triviya-lightBackground overflow-hidden shadow-xl pt-6 px-3 border-2 border-triviya-red text-black-900 rounded-lg">
                                <slot />
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
