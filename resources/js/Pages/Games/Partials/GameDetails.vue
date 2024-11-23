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


const props = defineProps({
    game: Object,
});

</script>

<template>
    <div>
    {{ game.name }}
    {{ formatDate(game.date_time) }}
    {{ game.players }}
</div>
</template>