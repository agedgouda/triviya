<script setup>
import { formatDate } from '@/utils';
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  message: String,
  flash: String
});

const form = useForm({
    question_text: '',
    question_type: 'text',
    modes: [],
});

const emit = defineEmits(['closeAddQuestion'])

function toggleMode(mode) {
    const index = form.modes.findIndex(selectedMode => selectedMode.id === mode.id);
    if (index !== -1) {
        // If mode is already in the array, remove it
        form.modes.splice(index, 1);
    } else {
        // If mode is not in the array, add it
        form.modes.push(mode);
    }
}

const addQuestion = async (status) => {
    try {
        // Post the form data
        await form.post(route('questions.store'), {
            preserveScroll: true,
            onSuccess: (page) => {
                // Handle success after the request completes
                if (status === 'close') {
                    emit('closeAddQuestion'); // Close the modal
                } else {
                    // Reset the form fields for another entry
                    form.reset();
                }
            },
        });
    } catch (error) {
        console.error('Error submitting form:', error);
    }
};



</script>
<template>
    <div class="p-6 mr-3 w-full  text-amber-400">
        <h1 class="font-bold text-xl mb-2"> Add A Question </h1>
        <span class="text-green-900" >{{ $page.props.flash.message }}</span>
        <form @submit.prevent="submit">
            <div>
                <div>
                    <InputLabel for="name" value="Question" />
                    <TextInput
                        id="name"
                        v-model="form.question_text"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.question_text" />
                </div>

                <div class="mt-3 flex">
                    <div class="w-40">
                        <InputLabel for="name" value="Type" />
                        <select
                            v-model="form.question_type"
                            class="border p-2 w-32 text-black"
                        >
                            <option value="text">Text</option>
                            <option value="date">Date</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.question_type" />
                    </div>
                    <div class="w-40">
                        <InputLabel for="name" value="Modes" />
                        <div v-for="mode in $page.props.modes" :key="mode.id" class="flex items-center">
                            <input
                                type="checkbox"
                                :value="mode.id"
                                @change="toggleMode(mode)"
                            />
                            <span class="ml-2">{{ mode.name }}</span>
                        </div>
                        <InputError class="mt-2" :message="form.errors.modes" />
                    </div>
                    <div class="mt-5 w-80">
                        <!-- Save and New and Save and Close buttons in the same row -->
                        <div class="flex justify-between">
                            <!-- Save and New button -->
                            <PrimaryButton type="button" @click="addQuestion()">
                                Save and New
                            </PrimaryButton>

                            <!-- Save and Close button -->
                            <PrimaryButton type="button" @click="addQuestion('close')" class="ml-2">
                                Save and Close
                            </PrimaryButton>
                        </div>

                        <!-- Cancel button aligned beneath Save and Close -->
                        <div class="text-right mt-2">
                            <DangerButton type="button" @click="$emit('closeAddQuestion')">
                                Cancel
                            </DangerButton>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>





"
