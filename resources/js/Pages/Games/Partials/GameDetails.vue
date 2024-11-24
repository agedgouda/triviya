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


const props = defineProps({
    game: Object,
});

</script>

<template>
    <div>

        <div class="mb-2">
            <div class="font-bold">{{ game.name }}</div>
            <div>{{ game.mode.name }}</div>
            <div>{{ formatDate(game.date_time) }}</div>
            <div class="font-bold">Players</div>
            <div>{{ game.players }}</div>
        </div>

        <div>Invite Players</div>
        {{ game.id }}
        <Invitees :gameId="game.id" :invtees="game.invitees" />
</div>
</template>