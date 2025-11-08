<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Flash from '@/Components/Flash.vue';
import { usePage } from '@inertiajs/vue3'
import { useFlash } from '@/Composables/useFlash';

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: false,
    },
    phpVersion: {
        type: String,
        required: false,
    },
    flashMessage: String,
});


const { setFlash } = useFlash();
const page = usePage();

onMounted(() => {
  if (props.flashMessage) {
    setFlash(props.flashMessage)
  }
  else if (page.props.accountDeletedMessage) {
    setFlash(page.props.accountDeletedMessage);
    page.props.accountDeletedMessage = null;
  }
})

</script>

<template>
    <Flash />
    <Head title="Welcome" />

     <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-3 text-center">
            <div class="text-2xl font-bold ">Welcome to TriviYa</div>
            The game where <span class="italic">you</span> are the trivia!
        </div>

        <div class="mb-3">
            It’s trivia with a twist. Players take a quick quiz about themselves, and those responses become the game that gets everyone
            guessing <span class="italic">who said that?</span>


        </div>

        <div class="text-center">
            <Link href="/home">
                <PrimaryButton type="button" class="mt-2">
                    <span>Get Started</span>
                </PrimaryButton>
            </Link>
        </div>

    </AuthenticationCard>
</template>
